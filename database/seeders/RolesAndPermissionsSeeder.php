<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // =========================================================================
        // PERMISSIONS - Organized by Feature
        // =========================================================================

        // 1. PRODUCT MANAGEMENT (7 permissions)
        $productPermissions = [
            'view products',
            'create products',
            'edit products',
            'delete products',
            'search products',
            'generate product sku',
            'manage product categories',
        ];

        // 2. INVENTORY MANAGEMENT (9 permissions)
        $inventoryPermissions = [
            'view inventory',
            'update stock',
            'adjust inventory',
            'view stock alerts',
            'manage low stock notifications',
            'view inventory reports',
            'transfer stock',
            'receive stock',
            'return stock',
        ];

        // 3. SALES & POS (7 permissions)
        $salesPermissions = [
            'view sales',
            'create sales',
            'void sales',
            'refund sales',
            'view daily sales',
            'view sales reports',
            'manage sales discounts',
        ];

        // 4. INVOICE MANAGEMENT (6 permissions)
        $invoicePermissions = [
            'view invoices',
            'create invoices',
            'print invoices',
            'email invoices',
            'manage invoice templates',
            'view invoice history',
        ];

        // 5. CUSTOMER MANAGEMENT (7 permissions)
        $customerPermissions = [
            'view customers',
            'create customers',
            'edit customers',
            'manage customers',
            'view customer history',
            'manage customer credit',
            'view customer statements',
        ];

        // 6. SUBSCRIPTION BILLING (9 permissions)
        $subscriptionPermissions = [
            'view subscriptions',
            'create subscriptions',
            'manage subscriptions',
            'cancel subscriptions',
            'view subscription plans',
            'manage subscription plans',
            'process recurring payments',
            'manage riders',
            'view subscription reports',
        ];

        // 7. LOYALTY & DISCOUNTS (9 permissions)
        $loyaltyPermissions = [
            'view loyalty points',
            'manage loyalty points',
            'redeem loyalty points',
            'view discount rules',
            'create discount rules',
            'edit discount rules',
            'delete discount rules',
            'apply discounts',
            'view points reports',
        ];

        // 8. CRM & MARKETING (8 permissions)
        $crmPermissions = [
            'view customers',
            'manage customer segments',
            'send bulk emails',
            'send bulk sms',
            'view campaign reports',
            'create marketing campaigns',
            'manage customer communications',
            'view communication history',
        ];

        // 9. REPORTING (10 permissions)
        $reportingPermissions = [
            'view sales reports',
            'view inventory reports',
            'view customer reports',
            'view financial reports',
            'view subscription reports',
            'view loyalty reports',
            'view product reports',
            'view daily summaries',
            'export reports',
            'schedule reports',
        ];

        // 10. DASHBOARD ACCESS (5 permissions)
        $dashboardPermissions = [
            'view cashier dashboard',
            'view admin dashboard',
            'view super admin dashboard',
            'view landing page',
            'manage landing page content',
        ];

        // 11. SYSTEM MANAGEMENT (6 permissions)
        $systemPermissions = [
            'manage users',
            'manage roles',
            'view audit logs',
            'manage system settings',
            'manage backup',
            'view system logs',
        ];

        // =========================================================================
        // CREATE ALL PERMISSIONS
        // =========================================================================

        $allPermissions = array_merge(
            $productPermissions,
            $inventoryPermissions,
            $salesPermissions,
            $invoicePermissions,
            $customerPermissions,
            $subscriptionPermissions,
            $loyaltyPermissions,
            $crmPermissions,
            $reportingPermissions,
            $dashboardPermissions,
            $systemPermissions
        );

        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web'
            ]);
        }

        // =========================================================================
        // CREATE ROLES
        // =========================================================================

        // 1. CASHIER ROLE - Daily operations only
        $cashierRole = Role::firstOrCreate(['name' => 'Cashier', 'guard_name' => 'web']);
        $cashierRole->syncPermissions([
            // Products
            'view products',
            'search products',
            
            // Sales
            'view sales',
            'create sales',
            'view daily sales',
            'apply discounts',
            
            // Invoices
            'view invoices',
            'create invoices',
            'print invoices',
            
            // Customers
            'view customers',
            'create customers',
            'view customer history',
            
            // Loyalty
            'view loyalty points',
            'redeem loyalty points',
            
            // Dashboard
            'view cashier dashboard',
        ]);

        // 2. ADMIN ROLE - Full operational control
        $adminRole = Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'web']);
        $adminRole->syncPermissions([
            // Product Management
            ...$productPermissions,
            
            // Inventory Management
            ...$inventoryPermissions,
            
            // Sales Management
            ...$salesPermissions,
            
            // Invoice Management
            ...$invoicePermissions,
            
            // Customer Management
            ...$customerPermissions,
            
            // Subscription Billing
            ...$subscriptionPermissions,
            
            // Loyalty & Discounts
            ...$loyaltyPermissions,
            
            // CRM & Marketing
            ...$crmPermissions,
            
            // Reporting
            ...$reportingPermissions,
            
            // Dashboard Access
            'view admin dashboard',
            'view cashier dashboard',
            'view landing page',
            
            // Limited System Management
            'manage users',
            'view audit logs',
            'manage system settings',
        ]);

        // 3. SUPER ADMIN ROLE - Full system control
        $superAdminRole = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'web']);
        $superAdminRole->syncPermissions($allPermissions);

        // =========================================================================
        // CREATE DEFAULT USERS
        // =========================================================================

        // Super Admin User
        $superAdmin = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            [
                'name' => 'Super Admin',
                'phone' => '+1234567890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $superAdmin->assignRole('Super Admin');

        // Admin User
        $admin = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'name' => 'Admin User',
                'phone' => '+1234567891',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $admin->assignRole('Admin');

        // Cashier User
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@example.com'],
            [
                'name' => 'Cashier User',
                'phone' => '+1234567892',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $cashier->assignRole('Cashier');

        // Additional Users
        $inventoryManager = User::firstOrCreate(
            ['email' => 'inventory@example.com'],
            [
                'name' => 'Inventory Manager',
                'phone' => '+1234567893',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $inventoryManager->assignRole('Admin'); // Assign Admin role to have inventory permissions

        $crmManager = User::firstOrCreate(
            ['email' => 'crm@example.com'],
            [
                'name' => 'CRM Manager',
                'phone' => '+1234567894',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );
        $crmManager->assignRole('Admin'); // Assign Admin role to have CRM permissions

        // =========================================================================
        // OUTPUT RESULTS
        // =========================================================================

        $this->command->info('========================================');
        $this->command->info('✅ ROLES AND PERMISSIONS SEEDED');
        $this->command->info('========================================');
        $this->command->info('📊 Total Permissions: ' . count($allPermissions));
        $this->command->info('');
        $this->command->info('👥 Roles Created:');
        $this->command->info('   - Super Admin (full access)');
        $this->command->info('   - Admin (operational control)');
        $this->command->info('   - Cashier (daily operations)');
        $this->command->info('');
        $this->command->info('👤 Default Users (password: password):');
        $this->command->info('   - superadmin@example.com');
        $this->command->info('   - admin@example.com');
        $this->command->info('   - cashier@example.com');
        $this->command->info('   - inventory@example.com');
        $this->command->info('   - crm@example.com');
        $this->command->info('========================================');
    }
}