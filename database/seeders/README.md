# Database Seeders

## UsersRolesPermissionsSeeder

This seeder populates the database with default users, roles, permissions, and their relationships.

### What Gets Seeded

#### 1. **Roles** (4 roles)
- **Admin** (id: 1) - Full system access with all permissions
- **Manager** (id: 2) - Manages inventory, menu, and sales oversight
- **Cashier** (id: 3) - Handles POS transactions and daily sales
- **Staff** (id: 4) - Basic access to dashboard and sales viewing

#### 2. **Permissions** (10 permissions)
1. `manage_users` - User Management
2. `view_dashboard` - View Dashboard
3. `access_pos` - Access POS
4. `manage_inventory` - Inventory Management
5. `manage_menu` - Menu Management
6. `view_sales` - Sales Overview
7. `maintenance` - System Maintenance
8. `manage_scholars` - Manage Scholars
9. `view_scholar_reports` - View Scholar Reports
10. `view_system_logs` - **View System Logs** ✨ (NEW!)

#### 3. **Role Permissions**
All roles have access to `view_system_logs`:

**Admin Role** (10 permissions):
- All permissions

**Manager Role** (8 permissions):
- view_dashboard, access_pos, manage_inventory, manage_menu
- view_sales, manage_scholars, view_scholar_reports
- ✅ view_system_logs

**Cashier Role** (4 permissions):
- view_dashboard, access_pos, view_sales
- ✅ view_system_logs

**Staff Role** (3 permissions):
- view_dashboard, view_sales
- ✅ view_system_logs

#### 4. **Users** (4 default users)

| ID | Username | Email | Password | Role | Status |
|----|----------|-------|----------|------|--------|
| 5 | admin | admin@gmail.com | password | Admin | Active |
| 6 | cashier | cashier@gmail.com | password | Cashier | Active |
| 7 | finance | finance@gmail.com | password | Staff | Active |
| 8 | manager | manager@gmail.com | password | Manager | Active |

### Usage

#### Run the Seeder

```bash
# Run specific seeder
php artisan db:seed --class=UsersRolesPermissionsSeeder

# Or run all seeders
php artisan db:seed
```

#### Fresh Migration with Seed

```bash
php artisan migrate:fresh --seed
```

### Important Notes

⚠️ **WARNINGS:**
- This seeder **TRUNCATES** (deletes all data) from: `users`, `roles`, `permissions`, `role_permissions`, and `user_permissions` tables
- Foreign key checks are temporarily disabled during seeding
- **Default password for all users is: `password`**
- **Change passwords immediately after first login in production!**

### Login Credentials

After seeding, you can login with:

```
Admin User:
Email: admin@gmail.com
Password: password

Cashier User:
Email: cashier@gmail.com
Password: password

Finance User:
Email: finance@gmail.com
Password: password

Manager User:
Email: manager@gmail.com
Password: password
```

### What This Seeder Does NOT Include

This seeder only handles users, roles, and permissions. It does NOT seed:
- Categories
- Products
- Inventory
- Menus
- Scholars
- Transactions
- Any other business data

### Customization

To customize the seeder:

1. Open `database/seeders/UsersRolesPermissionsSeeder.php`
2. Modify the arrays for roles, permissions, or users
3. Re-run the seeder

### Production Considerations

When deploying to production:

1. **Change default passwords** immediately
2. Consider **not running this seeder** in production if you already have users
3. Use `--force` flag for production: `php artisan db:seed --force`
4. Backup your database before running seeders
5. Create separate seeder for production admin user with strong password

### Verification

After seeding, verify the data:

```bash
php artisan tinker
```

Then run:
```php
use Illuminate\Support\Facades\DB;

echo 'Roles: ' . App\Models\Role::count() . PHP_EOL;
echo 'Permissions: ' . DB::table('permissions')->count() . PHP_EOL;
echo 'Users: ' . App\Models\User::count() . PHP_EOL;
echo 'Role Permissions: ' . DB::table('role_permissions')->count() . PHP_EOL;
echo 'User Permissions: ' . DB::table('user_permissions')->count() . PHP_EOL;

// Verify view_system_logs is assigned to all roles
echo 'Roles with view_system_logs: ' . DB::table('role_permissions')->where('permission_id', 10)->count();
```

Expected output:
```
Roles: 4
Permissions: 10
Users: 4
Role Permissions: 25
User Permissions: 14
Roles with view_system_logs: 4
```

### Troubleshooting

**Error: "Cannot truncate a table referenced in a foreign key constraint"**
- The seeder handles this by disabling foreign key checks temporarily
- If you still see this error, ensure your database user has proper privileges

**Error: "Duplicate entry for key 'PRIMARY'"**
- The tables are truncated before inserting, so this shouldn't happen
- If it does, manually truncate the tables first

**Users can't login after seeding**
- Ensure you're using the correct email and password
- Default password is `password` (all lowercase)
- Check that the user's status is 'active'

### Related Files

- Seeder: `database/seeders/UsersRolesPermissionsSeeder.php`
- Main Seeder: `database/seeders/DatabaseSeeder.php`
- Models: `app/Models/User.php`, `app/Models/Role.php`
- Migrations: Check `database/migrations/` for table structures

---

**Created**: 2025-09-30  
**Last Updated**: 2025-09-30  
**Version**: 1.0