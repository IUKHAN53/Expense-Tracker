<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\SpendingList;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database with a starter setup:
     * one admin account, the spending lists, and common categories.
     */
    public function run(): void
    {
        // --- Admin account (used for both the Filament admin panel and the app) ---
        User::updateOrCreate(
            ['email' => 'admin@expense.app'],
            [
                'name' => 'Admin',
                'password' => Hash::make('password'),
            ],
        );

        // --- Spending lists: 5 people + Home + Car ---
        // Rename "Person 1..5" and "Car" to taste in the admin panel.
        $lists = [
            ['name' => 'Person 1', 'type' => SpendingList::TYPE_PERSON, 'color' => '#6366f1', 'icon' => 'heroicon-o-user'],
            ['name' => 'Person 2', 'type' => SpendingList::TYPE_PERSON, 'color' => '#ec4899', 'icon' => 'heroicon-o-user'],
            ['name' => 'Person 3', 'type' => SpendingList::TYPE_PERSON, 'color' => '#f59e0b', 'icon' => 'heroicon-o-user'],
            ['name' => 'Person 4', 'type' => SpendingList::TYPE_PERSON, 'color' => '#10b981', 'icon' => 'heroicon-o-user'],
            ['name' => 'Person 5', 'type' => SpendingList::TYPE_PERSON, 'color' => '#06b6d4', 'icon' => 'heroicon-o-user'],
            ['name' => 'Home', 'type' => SpendingList::TYPE_HOUSEHOLD, 'color' => '#0ea5e9', 'icon' => 'heroicon-o-home'],
            ['name' => 'Car', 'type' => SpendingList::TYPE_VEHICLE, 'color' => '#ef4444', 'icon' => 'heroicon-o-truck'],
        ];

        foreach ($lists as $index => $list) {
            SpendingList::updateOrCreate(
                ['name' => $list['name']],
                [
                    'type' => $list['type'],
                    'color' => $list['color'],
                    'icon' => $list['icon'],
                    'sort_order' => $index,
                    'is_active' => true,
                ],
            );
        }

        // --- Common expense categories ---
        $categories = [
            ['name' => 'Groceries', 'color' => '#22c55e'],
            ['name' => 'Vegetables & Fruit', 'color' => '#84cc16'],
            ['name' => 'Meat', 'color' => '#dc2626'],
            ['name' => 'Dairy', 'color' => '#fbbf24'],
            ['name' => 'Snacks', 'color' => '#f97316'],
            ['name' => 'Household', 'color' => '#0ea5e9'],
            ['name' => 'Personal Care', 'color' => '#a855f7'],
            ['name' => 'Medicine', 'color' => '#14b8a6'],
            ['name' => 'Fuel', 'color' => '#ef4444'],
            ['name' => 'Utilities', 'color' => '#6366f1'],
            ['name' => 'Other', 'color' => '#64748b'],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(['name' => $category['name']], $category);
        }
    }
}
