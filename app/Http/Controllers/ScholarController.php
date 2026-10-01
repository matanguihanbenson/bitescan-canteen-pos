<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Scholar;
use App\Models\ScholarBalanceHistory;
use App\Services\ScholarService;
use App\Services\SystemLogService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\SettingsService;

class ScholarController extends Controller
{
    protected $scholarService;
    protected $settingsService;
    protected $logService;

    public function __construct(ScholarService $scholarService, SettingsService $settingsService, SystemLogService $logService)
    {
        $this->scholarService = $scholarService;
        $this->settingsService = $settingsService;
        $this->logService = $logService;
    }

    public function index()
    {
        $scholars = Scholar::select([
            'id',
            'student_id',
            'first_name',
            'last_name',
            'email',
            'program',
            'year_level',
            'current_balance',
            'last_allowance_date',
            'is_active',
            'created_at'
        ])->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();

        $stats = $this->scholarService->getSummaryStats();

        return view('scholars.index', compact('scholars', 'stats'));
    }

    public function getData(Request $request)
    {
        try {
            $scholars = Scholar::select([
                'id',
                'student_id',
                'first_name',
                'last_name',
                'email',
                'program',
                'year_level',
                'current_balance',
                'last_allowance_date',
                'is_active',
                'daily_allowance',
                'created_at'
            ])->orderBy('is_active', 'desc')->orderBy('created_at', 'desc')->get();

            $data = $scholars->map(function ($scholar) {
                $statusBadge = $scholar->is_active 
                    ? '<span class="badge bg-success">Active</span>' 
                    : '<span class="badge bg-secondary">Inactive</span>';
                
                $balance = '₱' . number_format($scholar->current_balance, 2);
                $lastClaim = $scholar->last_allowance_date 
                    ? $scholar->last_allowance_date->format('M d, Y') 
                    : 'Never';
                
                $actions = '
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-primary edit-scholar" data-id="' . $scholar->id . '">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button type="button" class="btn btn-sm btn-info view-history" data-id="' . $scholar->id . '">
                            <i class="bi bi-clock-history"></i>
                        </button>
                        <a href="' . route('scholars.toggle-status', $scholar->id) . '" class="btn btn-sm ' . ($scholar->is_active ? 'btn-warning' : 'btn-success') . '" onclick="event.preventDefault(); if(confirm(\'Are you sure?\')){document.getElementById(\'toggle-status-' . $scholar->id . '\').submit();}">
                            <i class="bi bi-' . ($scholar->is_active ? 'pause' : 'play') . '"></i>
                        </a>
                        <form id="toggle-status-' . $scholar->id . '" action="' . route('scholars.toggle-status', $scholar->id) . '" method="POST" style="display: none;">
                            ' . csrf_field() . '
                        </form>
                    </div>
                ';

                return [
                    'id' => $scholar->id,
                    'student_id' => $scholar->student_id,
                    'full_name' => $scholar->first_name . ' ' . $scholar->last_name,
                    'first_name' => $scholar->first_name,
                    'last_name' => $scholar->last_name,
                    'email' => $scholar->email ?: '-',
                    'program' => $scholar->program ?: '-',
                    'year_level' => $scholar->year_level ?: '-',
                    'balance' => $balance,
                    'current_balance' => $scholar->current_balance,
                    'daily_allowance' => $scholar->daily_allowance ?: 0,
                    'last_claim' => $lastClaim,
                    'last_allowance_date' => $scholar->last_allowance_date,
                    'status' => $statusBadge,
                    'is_active' => $scholar->is_active,
                    'actions' => $actions,
                    'created_at' => $scholar->created_at->format('M d, Y'),
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $data->values()->all(),
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => $scholars->count(),
                'recordsFiltered' => $scholars->count()
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading scholars data: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error loading data',
                'data' => [],
                'draw' => intval($request->input('draw', 1)),
                'recordsTotal' => 0,
                'recordsFiltered' => 0
            ], 500);
        }
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:50|unique:scholars,student_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:scholars,email',
            'contact_number' => 'nullable|string|max:20',
            'program' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:50',
            'daily_allowance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            DB::beginTransaction();

            $settings = $this->settingsService->getScholarSettings();
            $dailyAllowance = $request->daily_allowance ?? $settings['default_allowance'] ?? 70.00;

            // Create scholar with initial balance equal to daily allowance
            $scholar = Scholar::create([
                'student_id' => $request->student_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'program' => $request->program,
                'year_level' => $request->year_level,
                'daily_allowance' => $dailyAllowance,
                'current_balance' => $dailyAllowance, // Start with daily allowance amount
                'last_allowance_date' => today(), // Mark as received today
                'is_active' => true
            ]);

            // Create initial balance history record
            ScholarBalanceHistory::create([
                'scholar_id' => $scholar->id,
                'transaction_type' => 'credit',
                'amount' => $dailyAllowance,
                'previous_balance' => 0.00,
                'new_balance' => $dailyAllowance,
                'description' => 'Initial allowance - Account created',
                'transaction_date' => today(),
                'user_id' => auth()->id()
            ]);

            DB::commit();
            
            // Log scholar creation
            $this->logService->logScholar(
                'create',
                "Created new scholar: {$scholar->first_name} {$scholar->last_name} ({$scholar->student_id}) with initial allowance of ₱" . number_format($dailyAllowance, 2),
                null,
                [
                    'scholar_id' => $scholar->id,
                    'student_id' => $scholar->student_id,
                    'name' => $scholar->first_name . ' ' . $scholar->last_name,
                    'daily_allowance' => $dailyAllowance,
                    'initial_balance' => $dailyAllowance,
                ],
                $scholar->id,
                'success'
            );
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Scholar added successfully with initial allowance of ₱' . number_format($dailyAllowance, 2) . '!'
                ]);
            }
            
            return redirect()->route('scholars.index')->with('success', 'Scholar added successfully with initial allowance of ₱' . number_format($dailyAllowance, 2) . '!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating scholar: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create scholar: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to create scholar: ' . $e->getMessage())->withInput();
        }
    }

    public function show($id)
    {
        try {
            $scholar = Scholar::with('balanceHistory.user')->findOrFail($id);
            
            if (request()->ajax()) {
                return response()->json([
                    'success' => true,
                    'scholar' => [
                        'id' => $scholar->id,
                        'student_id' => $scholar->student_id,
                        'first_name' => $scholar->first_name,
                        'last_name' => $scholar->last_name,
                        'email' => $scholar->email,
                        'contact_number' => $scholar->contact_number,
                        'program' => $scholar->program,
                        'year_level' => $scholar->year_level,
                        'daily_allowance' => $scholar->daily_allowance,
                        'current_balance' => $scholar->current_balance,
                        'is_active' => $scholar->is_active
                    ]
                ]);
            }
            
            return view('scholars.show', compact('scholar'));
        } catch (\Exception $e) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Scholar not found'
                ], 404);
            }
            abort(404);
        }
    }

    public function edit($id)
    {
        $scholar = Scholar::findOrFail($id);
        return view('scholars.edit', compact('scholar'));
    }

    public function update(Request $request, $id)
    {
        $scholar = Scholar::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'student_id' => 'required|string|max:50|unique:scholars,student_id,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'nullable|email|unique:scholars,email,' . $id,
            'contact_number' => 'nullable|string|max:20',
            'program' => 'nullable|string|max:255',
            'year_level' => 'nullable|string|max:50',
            'daily_allowance' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return back()->withErrors($validator)->withInput();
        }

        try {
            $oldValues = [
                'student_id' => $scholar->student_id,
                'first_name' => $scholar->first_name,
                'last_name' => $scholar->last_name,
                'email' => $scholar->email,
                'daily_allowance' => $scholar->daily_allowance,
            ];
            
            $scholar->update([
                'student_id' => $request->student_id,
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
                'email' => $request->email,
                'contact_number' => $request->contact_number,
                'program' => $request->program,
                'year_level' => $request->year_level,
                'daily_allowance' => $request->daily_allowance ?? $scholar->daily_allowance,
            ]);

            $newValues = [
                'student_id' => $scholar->student_id,
                'first_name' => $scholar->first_name,
                'last_name' => $scholar->last_name,
                'email' => $scholar->email,
                'daily_allowance' => $scholar->daily_allowance,
            ];
            
            // Log scholar update
            $this->logService->logScholar(
                'update',
                "Updated scholar: {$scholar->first_name} {$scholar->last_name} ({$scholar->student_id})",
                $oldValues,
                $newValues,
                $scholar->id,
                'info'
            );

            if ($request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Scholar updated successfully!'
                ]);
            }

            return redirect()->route('scholars.index')->with('success', 'Scholar updated successfully!');
        } catch (\Exception $e) {
            Log::error('Error updating scholar: ' . $e->getMessage());
            
            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update scholar: ' . $e->getMessage()
                ], 500);
            }
            
            return back()->with('error', 'Failed to update scholar: ' . $e->getMessage())->withInput();
        }
    }

    public function toggleStatus($id)
    {
        try {
            $scholar = Scholar::findOrFail($id);
            $oldStatus = $scholar->is_active;
            $scholar->update(['is_active' => !$scholar->is_active]);

            $status = $scholar->is_active ? 'activated' : 'deactivated';
            
            // Log status change
            $this->logService->logScholar(
                'update',
                ucfirst($status) . " scholar: {$scholar->first_name} {$scholar->last_name} ({$scholar->student_id})",
                ['is_active' => $oldStatus],
                ['is_active' => $scholar->is_active],
                $scholar->id,
                'warning'
            );
            
            return redirect()->route('scholars.index')->with('success', "Scholar {$status} successfully!");
        } catch (\Exception $e) {
            Log::error('Error updating scholar status: ' . $e->getMessage());
            return back()->with('error', 'Failed to update scholar status: ' . $e->getMessage());
        }
    }

    public function getHistory($id)
    {
        try {
            $scholar = Scholar::findOrFail($id);
            $history = ScholarBalanceHistory::where('scholar_id', $id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($record) {
                    $user = null;
                    if ($record->user_id) {
                        $user = \App\Models\User::find($record->user_id);
                    }

                    return [
                        'date' => $record->created_at->format('M d, Y g:i A'),
                        'transaction_date' => $record->transaction_date ? $record->transaction_date->format('M d, Y') : $record->created_at->format('M d, Y'),
                        'type' => ucfirst($record->transaction_type),
                        'amount' => number_format($record->amount, 2),
                        'description' => $record->description ?? 'No description',
                        'previous_balance' => number_format($record->previous_balance ?? 0, 2),
                        'new_balance' => number_format($record->new_balance, 2),
                        'transaction_number' => $record->transaction_number ?? '-',
                        'processed_by' => $user ? $user->firstname . ' ' . $user->lastname : 'System',
                    ];
                });

            return response()->json([
                'success' => true,
                'scholar' => [
                    'id' => $scholar->id,
                    'student_id' => $scholar->student_id,
                    'first_name' => $scholar->first_name,
                    'last_name' => $scholar->last_name,
                    'full_name' => $scholar->first_name . ' ' . $scholar->last_name,
                    'current_balance' => number_format($scholar->current_balance, 2),
                    'daily_allowance' => number_format($scholar->daily_allowance, 2),
                    'last_allowance_date' => $scholar->last_allowance_date ? $scholar->last_allowance_date->format('M d, Y') : 'Never'
                ],
                'history' => $history
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading scholar history: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function getSummary()
    {
        try {
            $totalScholars = Scholar::count();
            $activeScholars = Scholar::where('is_active', true)->count();
            $claimedToday = Scholar::whereDate('last_allowance_date', today())->count();
            $totalBalance = Scholar::sum('current_balance');

            $todayAllowances = ScholarBalanceHistory::whereDate('created_at', today())
                ->where('transaction_type', 'credit')
                ->sum('amount');

            $percentageClaimed = $activeScholars > 0 ? round(($claimedToday / $activeScholars) * 100, 1) : 0;

            return response()->json([
                'success' => true,
                'stats' => [
                    'total_scholars' => $totalScholars,
                    'active_scholars' => $activeScholars,
                    'claimed_today' => $claimedToday,
                    'percentage_claimed' => $percentageClaimed,
                    'total_allowance_today' => $todayAllowances,
                    'total_balance_remaining' => $totalBalance,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error loading summary: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to load summary'
            ], 500);
        }
    }

    public function resetDailyAllowances()
    {
        try {
            // Optional: gate by permission if available
            if (auth()->user() && method_exists(auth()->user(), 'hasPermission')) {
                if (!auth()->user()->hasPermission('manage_scholars')) {
                    return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
                }
            }

            $scholars = Scholar::where('is_active', true)->get();
            $resetCount = 0;
            foreach ($scholars as $scholar) {
                // Force reset regardless of last_allowance_date (non-carryover)
                $previousBalance = $scholar->current_balance ?? 0;
                $newBalance = $scholar->daily_allowance ?? 0;
                
                // Skip if values are already correct to avoid duplicate entries
                if ((float)$previousBalance === (float)$newBalance && $scholar->last_allowance_date && $scholar->last_allowance_date->isToday()) {
                    continue;
                }

                DB::beginTransaction();
                try {
                    // Check if scholar already claimed today to avoid duplicates
                    if ($scholar->last_allowance_date && $scholar->last_allowance_date->isToday()) {
                        DB::rollBack();
                        continue;
                    }

                    $scholar->update([
                        'current_balance' => $newBalance,
                        'last_allowance_date' => today()
                    ]);

                    ScholarBalanceHistory::create([
                        'scholar_id' => $scholar->id,
                        'transaction_type' => 'credit',
                        'amount' => $newBalance,
                        'previous_balance' => $previousBalance,
                        'new_balance' => $newBalance,
                        'description' => 'Manual daily reset (no carryover)',
                        'transaction_date' => today(),
                        'user_id' => auth()->id()
                    ]);

                    DB::commit();
                    $resetCount++;
                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Manual reset error for scholar ID ' . $scholar->id . ': ' . $e->getMessage());
                }
            }

            // Log the bulk reset operation
            if ($resetCount > 0) {
                $this->logService->logScholar(
                    'update',
                    "Manually reset daily allowances for {$resetCount} scholar(s)",
                    null,
                    ['reset_count' => $resetCount, 'date' => today()->toDateString()],
                    null,
                    'warning'
                );
            }

            return response()->json([
                'success' => true,
                'message' => "Daily allowances reset for {$resetCount} scholar(s).",
                'count' => $resetCount
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to reset allowances: ' . $e->getMessage()
            ], 500);
        }
    }

    public function validateStudent(Request $request)
    {
        try {
            $student = Scholar::where('student_id', $request->student_id)
                ->where('is_active', true)
                ->first();

            if (!$student) {
                return response()->json([
                    'valid' => false,
                    'message' => 'Student ID not found or inactive'
                ], 404);
            }

            return response()->json([
                'valid' => true,
                'scholar' => [
                    'id' => $student->id,
                    'student_id' => $student->student_id,
                    'full_name' => $student->first_name . ' ' . $student->last_name,
                    'current_balance' => $student->current_balance,
                    'daily_allowance' => $student->daily_allowance,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Error validating student: ' . $e->getMessage());
            return response()->json([
                'valid' => false,
                'message' => 'Validation failed'
            ], 500);
        }
    }


    public function getAllowanceReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $allowanceHistory = ScholarBalanceHistory::with(['scholar', 'processedBy'])
            ->where('transaction_type', 'credit')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'date' => $record->created_at->format('M d, Y'),
                    'scholar_name' => $record->scholar->first_name . ' ' . $record->scholar->last_name,
                    'student_id' => $record->scholar->student_id,
                    'amount' => $record->amount,
                    'description' => $record->description,
                    'processed_by' => $record->processedBy ? $record->processedBy->name : 'System'
                ];
            });

        $summary = [
            'total_allowances' => $allowanceHistory->count(),
            'total_amount' => $allowanceHistory->sum('amount'),
            'unique_scholars' => $allowanceHistory->unique('student_id')->count(),
            'date_range' => $dateFrom . ' to ' . $dateTo
        ];

        return response()->json([
            'success' => true,
            'data' => $allowanceHistory,
            'summary' => $summary
        ]);
    }

    public function getUsageReport(Request $request)
    {
        $dateFrom = $request->input('date_from', now()->startOfMonth()->toDateString());
        $dateTo = $request->input('date_to', now()->toDateString());

        $usageHistory = ScholarBalanceHistory::with(['scholar', 'processedBy'])
            ->where('transaction_type', 'debit')
            ->whereBetween('created_at', [$dateFrom, $dateTo])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($record) {
                return [
                    'date' => $record->created_at->format('M d, Y'),
                    'scholar_name' => $record->scholar->first_name . ' ' . $record->scholar->last_name,
                    'student_id' => $record->scholar->student_id,
                    'amount' => $record->amount,
                    'description' => $record->description,
                    'processed_by' => $record->processedBy ? $record->processedBy->name : 'System'
                ];
            });

        $summary = [
            'total_transactions' => $usageHistory->count(),
            'total_spent' => $usageHistory->sum('amount'),
            'unique_scholars' => $usageHistory->unique('student_id')->count(),
            'date_range' => $dateFrom . ' to ' . $dateTo
        ];

        return response()->json([
            'success' => true,
            'data' => $usageHistory,
            'summary' => $summary
        ]);
    }

    public function reports()
    {
        $stats = $this->scholarService->getSummaryStats();
        return view('scholars.reports', compact('stats'));
    }

    public function getReportsData(Request $request)
    {
        try {
            $dateFrom = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : now()->startOfMonth();
            $dateTo = $request->date_to ? \Carbon\Carbon::parse($request->date_to) : now()->endOfMonth();
            
            $query = ScholarBalanceHistory::with(['scholar', 'user'])
                ->whereBetween('transaction_date', [$dateFrom, $dateTo]);
                
            if ($request->scholar_id) {
                $query->where('scholar_id', $request->scholar_id);
            }
            
            if ($request->transaction_type) {
                $query->where('transaction_type', $request->transaction_type);
            }
            
            $transactions = $query->orderBy('transaction_date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($transaction) {
                    return [
                        'id' => $transaction->id,
                        'date' => $transaction->transaction_date->format('M d, Y'),
                        'time' => $transaction->created_at->format('h:i A'),
                        'scholar_name' => $transaction->scholar ? $transaction->scholar->first_name . ' ' . $transaction->scholar->last_name : 'Unknown',
                        'student_id' => $transaction->scholar ? $transaction->scholar->student_id : 'N/A',
                        'type' => ucfirst($transaction->transaction_type),
                        'type_badge' => $transaction->transaction_type === 'credit' ? 'success' : 'danger',
                        'amount' => number_format($transaction->amount, 2),
                        'previous_balance' => number_format($transaction->previous_balance, 2),
                        'new_balance' => number_format($transaction->new_balance, 2),
                        'description' => $transaction->description ?? 'No description',
                        'transaction_number' => $transaction->transaction_number ?? '-',
                        'processed_by' => $transaction->user ? $transaction->user->firstname . ' ' . $transaction->user->lastname : 'System'
                    ];
                });
                
            // Calculate summary stats for the filtered data
            $summary = [
                'total_transactions' => $transactions->count(),
                'total_credits' => $transactions->where('type', 'Credit')->sum(function($t) { return (float)str_replace(',', '', $t['amount']); }),
                'total_debits' => $transactions->where('type', 'Debit')->sum(function($t) { return (float)str_replace(',', '', $t['amount']); }),
                'unique_scholars' => $transactions->pluck('student_id')->unique()->count()
            ];
            
            return response()->json([
                'success' => true,
                'data' => $transactions->values()->all(),
                'summary' => $summary
            ]);
            
        } catch (\Exception $e) {
            Log::error('Scholar reports data error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error fetching reports data',
                'data' => [],
                'summary' => [
                    'total_transactions' => 0,
                    'total_credits' => 0,
                    'total_debits' => 0,
                    'unique_scholars' => 0
                ]
            ]);
        }
    }

    public function exportReport(Request $request)
    {
        try {
            $dateFrom = $request->date_from ? \Carbon\Carbon::parse($request->date_from) : now()->startOfMonth();
            $dateTo = $request->date_to ? \Carbon\Carbon::parse($request->date_to) : now()->endOfMonth();
            
            $query = ScholarBalanceHistory::with(['scholar', 'user'])
                ->whereBetween('transaction_date', [$dateFrom, $dateTo]);
                
            if ($request->scholar_id) {
                $query->where('scholar_id', $request->scholar_id);
            }
            
            if ($request->transaction_type) {
                $query->where('transaction_type', $request->transaction_type);
            }
            
            $transactions = $query->orderBy('transaction_date', 'desc')->get();
            
            if ($request->format === 'pdf') {
                return $this->exportPDF($transactions, $dateFrom, $dateTo);
            } else {
                return $this->exportExcel($transactions, $dateFrom, $dateTo);
            }
            
        } catch (\Exception $e) {
            Log::error('Scholar report export error: ' . $e->getMessage());
            return back()->with('error', 'Failed to export report: ' . $e->getMessage());
        }
    }

    private function exportPDF($transactions, $dateFrom, $dateTo)
    {
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('scholars.reports.pdf', [
            'transactions' => $transactions,
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'totalCredits' => $transactions->where('transaction_type', 'credit')->sum('amount'),
            'totalDebits' => $transactions->where('transaction_type', 'debit')->sum('amount'),
            'uniqueScholars' => $transactions->pluck('scholar_id')->unique()->count()
        ]);
        
        return $pdf->download('scholar-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.pdf');
    }

    private function exportExcel($transactions, $dateFrom, $dateTo)
    {
        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ScholarReportExport($transactions),
            'scholar-report-' . $dateFrom->format('Y-m-d') . '-to-' . $dateTo->format('Y-m-d') . '.xlsx'
        );
    }

    public function getScholarsForFilter()
    {
        $scholars = Scholar::select('id', 'student_id', 'first_name', 'last_name')
            ->where('is_active', true)
            ->orderBy('first_name')
            ->get()
            ->map(function ($scholar) {
                return [
                    'id' => $scholar->id,
                    'name' => $scholar->first_name . ' ' . $scholar->last_name . ' (' . $scholar->student_id . ')'
                ];
            });
            
        return response()->json($scholars);
    }
}
