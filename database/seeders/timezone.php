<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class timezone extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('timezone_format')->truncate();
        $timezone=[[
            'name' =>'Pacific/Midway',
            'timezone' => '(GMT-11:00) Midway Island, Samoa',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Adak',
            'timezone' => '(GMT-10:00) Hawaii-Aleutian',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Etc/GMT+10',
            'timezone' => '(GMT-10:00) Hawaii',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Marquesas',
            'timezone' => '(GMT-09:30) Marquesas Islands',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Gambier',
            'timezone' => '(GMT-09:00) Gambier Islands',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Anchorage',
            'timezone' => '(GMT-09:00) Alaska',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Ensenada',
            'timezone' => '(GMT-08:00) Tijuana, Baja California',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Etc/GMT+8',
            'timezone' => '(GMT-08:00) Pitcairn Islands',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Los_Angeles',
            'timezone' => '(GMT-08:00) Pacific Time (US & Canada)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Denver',
            'timezone' => '(GMT-07:00) Mountain Time (US & Canada)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Chihuahua',
            'timezone' => '(GMT-07:00) Chihuahua, La Paz, Mazatlan',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Dawson_Creek',
            'timezone' => '(GMT-07:00) Arizona',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Belize',
            'timezone' => '(GMT-06:00) Saskatchewan, Central America',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Cancun',
            'timezone' => '(GMT-06:00) Guadalajara, Mexico City, Monterrey',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Chile/EasterIsland',
            'timezone' => '(GMT-06:00) Easter Island',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Chicago',
            'timezone' => '(GMT-06:00) Central Time (US & Canada)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/New_York',
            'timezone' => '(GMT-05:00) Eastern Time (US & Canada)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Havana',
            'timezone' => '(GMT-05:00) Cuba',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Bogota',
            'timezone' => '(GMT-05:00) Bogota, Lima, Quito, Rio Branco',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Caracas',
            'timezone' => '(GMT-04:30) Caracas',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Santiago',
            'timezone' => '(GMT-04:00) Santiago',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/La_Paz',
            'timezone' => '(GMT-04:00) La Paz',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Atlantic/Stanley',
            'timezone' => '(GMT-04:00) Faukland Islands',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Campo_Grande',
            'timezone' => '(GMT-04:00) Brazil',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Goose_Bay',
            'timezone' => '(GMT-04:00) Atlantic Time (Goose Bay)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Glace_Bay',
            'timezone' => '(GMT-04:00) Atlantic Time (Canada)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/St_Johns',
            'timezone' => '(GMT-03:30) Newfoundland',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Araguaina',
            'timezone' => '(GMT-03:00) UTC-3',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Montevideo',
            'timezone' => '(GMT-03:00) Montevideo',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Miquelon',
            'timezone' => '(GMT-03:00) Miquelon, St. Pierre',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Godthab',
            'timezone' => '(GMT-03:00) Greenland',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Argentina/Buenos_Aires',
            'timezone' => '(GMT-03:00) Buenos Aires',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Sao_Paulo',
            'timezone' => '(GMT-03:00) Brasilia',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'America/Noronha',
            'timezone' => '(GMT-02:00) Mid-Atlantic',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Atlantic/Cape_Verde',
            'timezone' => '(GMT-01:00) Cape Verde Is',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Atlantic/Azores',
            'timezone' => '(GMT-01:00) Azores',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Belfast',
            'timezone' => '(GMT) Greenwich Mean Time : Belfast',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Dublin',
            'timezone' => '(GMT) Greenwich Mean Time : Dublin',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Lisbon',
            'timezone' => '(GMT) Greenwich Mean Time : Lisbon',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/London',
            'timezone' => '(GMT) Greenwich Mean Time : London',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Africa/Abidjan',
            'timezone' => '(GMT) Monrovia, Reykjavik',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Amsterdam',
            'timezone' => '(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Belgrade',
            'timezone' => '(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Brussels',
            'timezone' => '(GMT+01:00) Brussels, Copenhagen, Madrid, Paris',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Africa/Algiers',
            'timezone' => '(GMT+01:00) West Central Africa',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Africa/Windhoek',
            'timezone' => '(GMT+01:00) Windhoek',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Beirut',
            'timezone' => '(GMT+02:00) Beirut',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Africa/Cairo',
            'timezone' => '(GMT+02:00) Cairo',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Gaza',
            'timezone' => '(GMT+02:00) Gaza',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Africa/Blantyre',
            'timezone' => '(GMT+02:00) Harare, Pretoria',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Jerusalem',
            'timezone' => '(GMT+02:00) Jerusalem',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Minsk',
            'timezone' => '(GMT+02:00) Minsk',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Damascus',
            'timezone' => '(GMT+02:00) Syria',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Europe/Moscow',
            'timezone' => '(GMT+03:00) Moscow, St. Petersburg, Volgograd',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Africa/Addis_Ababa',
            'timezone' => '(GMT+03:00) Nairobi',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Tehran',
            'timezone' => '(GMT+03:30) Tehran',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Dubai',
            'timezone' => '(GMT+04:00) Abu Dhabi, Muscat',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Yerevan',
            'timezone' => '(GMT+04:00) Yerevan',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Kabul',
            'timezone' => '(GMT+04:30) Kabul',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Yekaterinburg',
            'timezone' => '(GMT+04:30) Kabul',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Tashkent',
            'timezone' => '(GMT+05:00) Tashkent',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Kolkata',
            'timezone' => '(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Katmandu',
            'timezone' => '(GMT+05:45) Kathmandu',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Dhaka',
            'timezone' => '(GMT+06:00) Astana, Dhaka',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Novosibirsk',
            'timezone' => '(GMT+06:00) Novosibirsk',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Rangoon',
            'timezone' => '(GMT+06:30) Yangon (Rangoon)',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Bangkok',
            'timezone' => '(GMT+07:00) Bangkok, Hanoi, Jakarta',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Krasnoyarsk',
            'timezone' => '(GMT+07:00) Krasnoyarsk',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Hong_Kong',
            'timezone' => '(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Irkutsk',
            'timezone' => '(GMT+08:00) Irkutsk, Ulaan Bataar',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Perth',
            'timezone' => '(GMT+08:00) Perth',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Eucla',
            'timezone' => '(GMT+08:45) Eucla',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Tokyo',
            'timezone' => '(GMT+09:00) Osaka, Sapporo, Tokyo',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Seoul',
            'timezone' => '(GMT+09:00) Seoul',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Yakutsk',
            'timezone' => '(GMT+09:00) Yakutsk',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Adelaide',
            'timezone' => '(GMT+09:30) Adelaide',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Darwin',
            'timezone' => '(GMT+09:30) Darwin',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Brisbane',
            'timezone' => '(GMT+10:00) Brisbane',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Hobart',
            'timezone' => '(GMT+10:00) Hobart',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Vladivostok',
            'timezone' => '(GMT+10:00) Vladivostok',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Australia/Lord_Howe',
            'timezone' => '(GMT+10:30) Lord Howe Island',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Etc/GMT-11',
            'timezone' => '(GMT+11:00) Solomon Is., New Caledonia',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Magadan',
            'timezone' => '(GMT+11:00) Magadan',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Norfolk',
            'timezone' => '(GMT+11:30) Norfolk Island',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Asia/Anadyr',
            'timezone' => '(GMT+12:00) Anadyr, Kamchatka',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Auckland',
            'timezone' => '(GMT+12:00) Auckland, Wellington',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Etc/GMT-12',
            'timezone' => '(GMT+12:00) Fiji, Kamchatka, Marshall Is',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Chatham',
            'timezone' => '(GMT+12:45) Chatham Islands',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Tongatapu',
            'timezone' => '(GMT+13:00) Nukulofa',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ],[
            'name' =>'Pacific/Kiritimati',
            'timezone' => '(GMT+14:00) Kiritimati',
             'status' => '1',
            'created_at' => date("Y-m-d H:i:s"),
            'updated_at' => date("Y-m-d H:i:s"),
            
        ]];
        
        DB::table('timezone_format')->insert($timezone);
    }
}
