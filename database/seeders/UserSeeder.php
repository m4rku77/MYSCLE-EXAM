<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('users')->insert([

            [
                'id' => 1,
                'name' => 'Dejuan Cummerata',
                'email' => 'liliana24@example.com',
                'role' => 'user',

                'goal' => 'Muscle Gain',
                'weight' => '82kg',
                'height' => '182cm',
                'age' => 23,
                'gender' => 'Male',
                'bio' => 'Focused on strength and hypertrophy.',
                'completed_workouts' => 42,

                'profile_photo' => 'https://preview.redd.it/anyone-do-i-need-a-new-pfp-v0-lpbobx55hphe1.jpeg?width=640&crop=smart&auto=webp&s=9cab5096ad9ac8f924657aa659c3870a3bcfadc9',

                'password' => bcrypt('password'),
                'remember_token' => 'Cve9tk2zFw',

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 2,
                'name' => 'Katharina Wiegand',
                'email' => 'raquel.stanton@example.net',
                'role' => 'user',

                'goal' => 'Fat Loss',
                'weight' => '64kg',
                'height' => '168cm',
                'age' => 25,
                'gender' => 'Female',
                'bio' => 'Improving conditioning and mobility.',
                'completed_workouts' => 26,

                'profile_photo' => 'https://static.vecteezy.com/system/resources/thumbnails/060/843/811/small/close-up-of-raindrops-on-leaves-hd-background-luxury-hd-wallpaper-image-trendy-background-illustration-free-photo.jpg',

                'password' => bcrypt('password'),
                'remember_token' => 'mVTrS63xk8',

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 3,
                'name' => "Mr. Milford O'Kon",
                'email' => 'garnet.farrell@example.com',
                'role' => 'user',

                'goal' => 'Strength',
                'weight' => '91kg',
                'height' => '188cm',
                'age' => 29,
                'gender' => 'Male',
                'bio' => 'Powerlifting focused athlete.',
                'completed_workouts' => 61,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => 'H8unTVP844',

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 4,
                'name' => 'mark',
                'email' => 'mark@gmail.com',
                'role' => 'trainer',

                'goal' => null,
                'weight' => null,
                'height' => null,
                'age' => null,
                'gender' => null,
                'bio' => 'Online trainer and coach.',
                'completed_workouts' => 0,

                'profile_photo' => 'profiles/s1ZYbgOOoncUsALW8Ju2fdrhjZCpRIHmh4hnzOr7.jpg',

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 5,
                'name' => 'leons',
                'email' => 'leons@gmail.com',
                'role' => 'admin',

                'goal' => null,
                'weight' => null,
                'height' => null,
                'age' => null,
                'gender' => null,
                'bio' => 'Administrator account.',
                'completed_workouts' => 0,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 6,
                'name' => 'Markuss Jansons',
                'email' => 'jjansonsmarkuss@gmail.com',
                'role' => 'user',

                'goal' => 'Athletic Performance',
                'weight' => '78kg',
                'height' => '180cm',
                'age' => 18,
                'gender' => 'Male',
                'bio' => 'Hybrid athlete focused on performance.',
                'completed_workouts' => 58,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 7,
                'name' => 'cezars jekabsons',
                'email' => 'cezarsjekabs@gmail.com',
                'role' => 'user',

                'goal' => 'Muscle Gain',
                'weight' => '85kg',
                'height' => '184cm',
                'age' => 20,
                'gender' => 'Male',
                'bio' => 'Focused on aesthetics and size.',
                'completed_workouts' => 33,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 8,
                'name' => 'lote ulmane',
                'email' => 'lote@gmail.com',
                'role' => 'user',

                'goal' => 'Fat Loss',
                'weight' => '58kg',
                'height' => '165cm',
                'age' => 19,
                'gender' => 'Female',
                'bio' => 'Working on consistency and cardio.',
                'completed_workouts' => 19,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 9,
                'name' => 'martins sesks',
                'email' => 'sesks@gmail.com',
                'role' => 'user',

                'goal' => 'Strength',
                'weight' => '94kg',
                'height' => '190cm',
                'age' => 24,
                'gender' => 'Male',
                'bio' => 'Heavy compound movement athlete.',
                'completed_workouts' => 71,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 10,
                'name' => 'Roberts Jaunzems',
                'email' => 'robis@gmail.com',
                'role' => 'user',

                'goal' => 'Athletic Performance',
                'weight' => '81kg',
                'height' => '183cm',
                'age' => 22,
                'gender' => 'Male',
                'bio' => 'Explosive athlete focused on performance.',
                'completed_workouts' => 49,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 11,
                'name' => 'a a',
                'email' => 'a@com.lv',
                'role' => 'user',

                'goal' => 'General Fitness',
                'weight' => '74kg',
                'height' => '176cm',
                'age' => 21,
                'gender' => 'Male',
                'bio' => 'Building healthy habits.',
                'completed_workouts' => 12,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'id' => 12,
                'name' => 'raimonds kristovskis',
                'email' => 'kristovskis@gmail.com',
                'role' => 'user',

                'goal' => 'Muscle Gain',
                'weight' => '88kg',
                'height' => '186cm',
                'age' => 27,
                'gender' => 'Male',
                'bio' => 'Bodybuilding focused athlete.',
                'completed_workouts' => 54,

                'profile_photo' => null,

                'password' => bcrypt('password'),
                'remember_token' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
}
