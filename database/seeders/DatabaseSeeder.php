<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Branch;
use App\Models\Category;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Create Default Users
        $user = User::create([
            'name' => 'مؤمن يحيى',
            'first_name' => 'مؤمن',
            'last_name' => 'يحيى',
            'email' => 'user@example.com',
            'phone' => '01063888667',
            'password' => Hash::make('password123'),
            'role' => 'customer',
        ]);

        $admin = User::create([
            'name' => 'مدير النظام',
            'first_name' => 'مدير',
            'last_name' => 'النظام',
            'email' => 'admin@example.com',
            'phone' => '01000000000',
            'password' => Hash::make('admin123456'),
            'role' => 'admin',
        ]);

        // 2. Create Categories
        $catArabic = Category::create([
            'name' => 'كتب عربيه',
            'slug' => 'arabic-books',
            'description' => 'تشكيلة مميزة من الكتب والروايات باللغة العربية',
            'is_active' => true,
        ]);

        $catEnglish = Category::create([
            'name' => 'كتب انجليزية',
            'slug' => 'english-books',
            'description' => 'أفضل الكتب والمراجع الإنجليزية في شتى المجالات',
            'is_active' => true,
        ]);

        $catTech = Category::create([
            'name' => 'كتب البرمجة والتكنولوجيا',
            'slug' => 'programming-tech',
            'description' => 'كتب متخصصة في تطوير البرمجيات والذكاء الاصطناعي وتطبيقات الهواتف',
            'is_active' => true,
        ]);

        // 3. Create Authors
        $authorRay = Author::create([
            'name' => 'Ray Wenderlich Team',
            'slug' => 'ray-wenderlich-team',
            'bio' => 'مجموعة من خبراء تطوير تطبيقات الهواتف والـ Flutter و iOS',
        ]);

        $authorMartin = Author::create([
            'name' => 'Robert C. Martin (Uncle Bob)',
            'slug' => 'robert-c-martin',
            'bio' => 'مؤلف شهير في هندسة البرمجيات والتصميم النظيف للكود',
        ]);

        $authorJames = Author::create([
            'name' => 'جيمس كلير (James Clear)',
            'slug' => 'james-clear',
            'bio' => 'كاتب ومتحدث متخصص في العادات وصناعة القرار والتطوير الذاتي',
        ]);

        // 4. Create Books
        Book::create([
            'category_id' => $catTech->id,
            'author_id' => $authorRay->id,
            'author_name' => 'Ray Wenderlich Team',
            'title' => 'Flutter Apprentice',
            'slug' => 'flutter-apprentice',
            'description' => 'دليلك الشامل لتعلم بناء تطبيقات الجوال المتقدمة باستخدام Flutter & Dart من الصفر حتى الاحتراف.',
            'price' => 350.00,
            'old_price' => 450.00,
            'stock' => 25,
            'cover_image' => 'assets/images/product-1.webp',
            'is_featured' => true,
            'is_active' => true,
            'pages' => 480,
            'language' => 'en',
        ]);

        Book::create([
            'category_id' => $catTech->id,
            'author_id' => $authorMartin->id,
            'author_name' => 'Robert C. Martin',
            'title' => 'Clean Code: A Handbook of Agile Software Craftsmanship',
            'slug' => 'clean-code',
            'description' => 'الكتاب المرجعي لكل مبرمج يسعى لكتابة كود نظيف وقابل للصيانة والاختبار.',
            'price' => 400.00,
            'old_price' => 520.00,
            'stock' => 15,
            'cover_image' => 'assets/images/product-2.webp',
            'is_featured' => true,
            'is_active' => true,
            'pages' => 464,
            'language' => 'en',
        ]);

        Book::create([
            'category_id' => $catArabic->id,
            'author_id' => $authorJames->id,
            'author_name' => 'جيمس كلير',
            'title' => 'العادات الذرية (Atomic Habits - مترجم)',
            'slug' => 'atomic-habits-arabic',
            'description' => 'تغييرات صغيرة ونتائج مذهلة، الطريقة السهلة والمثبتة لبناء عادات جيدة والتخلص من العادات السيئة.',
            'price' => 220.00,
            'old_price' => 280.00,
            'stock' => 50,
            'cover_image' => 'assets/images/product-3.webp',
            'is_featured' => true,
            'is_active' => true,
            'pages' => 320,
            'language' => 'ar',
        ]);

        Book::create([
            'category_id' => $catEnglish->id,
            'author_id' => null,
            'author_name' => 'Andrew Ng',
            'title' => 'Machine Learning Yearning',
            'slug' => 'machine-learning-yearning',
            'description' => 'دليل عملي لتصميم وتنفيذ مشاريع تعلم الآلة والذكاء الاصطناعي بنجاح.',
            'price' => 300.00,
            'old_price' => 380.00,
            'stock' => 20,
            'cover_image' => 'assets/images/product-4.webp',
            'is_featured' => false,
            'is_active' => true,
            'pages' => 240,
            'language' => 'en',
        ]);

        // 5. Create Branches
        Branch::create([
            'name' => 'فرع طنطا',
            'city' => 'طنطا',
            'address' => 'ش بطرس مع سعيد امام المركز الطبى - طنطا.',
            'phone' => '01063888667',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'فرع اسكندرية',
            'city' => 'الاسكندرية',
            'address' => 'ش جمال عبد الناصر - تحت كوبرى 45 - ميامى.',
            'phone' => '01063888667',
            'is_active' => true,
        ]);

        Branch::create([
            'name' => 'فرع المحلة',
            'city' => 'المحلة الكبرى',
            'address' => 'ش شكري الكواتلي مع ش عبد العزيز امام البنك الاهلي.',
            'phone' => '01063888667',
            'is_active' => true,
        ]);
    }
}
