<?php

namespace Database\Seeders;

use App\Models\LaravelVersion;
use Illuminate\Database\Seeder;

class LaravelVersionSeeder extends Seeder
{
    public function run()
    {
        LaravelVersion::truncate();

        foreach ($this->versions() as $version) {
            LaravelVersion::create($version);
        }
    }

    public function versions()
    {
        return [
            [
                'major' => 10,
                'released_at' => '2023-02-07',
                'ends_bugfixes_at' => '2024-08-07',
                'ends_securityfixes_at' => '2025-02-07',
            ],
            [
                'major' => 9,
                'released_at' => '2022-02-08',
                'ends_bugfixes_at' => '2023-08-08',
                'ends_securityfixes_at' => '2024-02-08',
            ],
            [
                'major' => 8,
                'released_at' => '2020-09-08',
                'ends_bugfixes_at' => '2022-07-26',
                'ends_securityfixes_at' => '2023-01-24',
            ],
            [
                'major' => 7,
                'released_at' => '2020-03-03',
                'ends_bugfixes_at' => '2020-10-06',
                'ends_securityfixes_at' => '2021-03-03',
            ],
            [
                'major' => 6,
                'released_at' => '2019-09-03',
                'ends_bugfixes_at' => '2021-09-07',
                'ends_securityfixes_at' => '2022-09-06',
                'is_lts' => true,
            ],
            [
                'major' => 5,
                'minor' => 8,
                'released_at' => '2019-02-26',
                'ends_bugfixes_at' => '2019-08-26',
                'ends_securityfixes_at' => '2020-02-26',
            ],
            [
                'major' => 5,
                'minor' => 7,
                'released_at' => '2018-09-04',
                'ends_bugfixes_at' => '2019-03-04',
                'ends_securityfixes_at' => '2019-09-04',
            ],
            [
                'major' => 5,
                'minor' => 6,
                'released_at' => '2018-02-07',
                'ends_bugfixes_at' => '2018-08-07',
                'ends_securityfixes_at' => '2019-02-07',
            ],
            [
                'major' => 5,
                'minor' => 5,
                'released_at' => '2017-08-30',
                'ends_bugfixes_at' => '2019-08-30',
                'ends_securityfixes_at' => '2020-08-30',
                'is_lts' => true,
            ],
            [
                'major' => 5,
                'minor' => 4,
                'released_at' => '2017-01-24',
                'ends_bugfixes_at' => '2017-7-24',
                'ends_securityfixes_at' => '2018-01-24',
            ],
            [
                'major' => 5,
                'minor' => 3,
                'released_at' => '2016-08-23',
                'ends_bugfixes_at' => '2017-02-23',
                'ends_securityfixes_at' => '2017-08-23',
            ],
            [
                'major' => 5,
                'minor' => 2,
                'released_at' => '2015-12-21',
                'ends_bugfixes_at' => '2016-06-21',
                'ends_securityfixes_at' => '2016-12-12',
            ],
            [
                'major' => 5,
                'minor' => 1,
                'released_at' => '2015-06-09',
                'ends_bugfixes_at' => '2017-06-09',
                'ends_securityfixes_at' => '2018-06-09',
                'is_lts' => true,
            ],
            [
                'major' => 5,
                'minor' => 0,
                'released_at' => '2015-02-04',
                'ends_bugfixes_at' => '2015-08-04',
                'ends_securityfixes_at' => '2016-02-04',
            ],
            [
                'major' => 4,
                'minor' => 2,
                'released_at' => '2014-06-01',
            ],
            [
                'major' => 4,
                'minor' => 1,
                'released_at' => '2013-12-12',
            ],
            [
                'major' => 4,
                'minor' => 0,
                'released_at' => '2013-05-28',
            ],
            [
                'major' => 3,
                'minor' => 2,
                'released_at' => '2012-05-22',
            ],
            [
                'major' => 3,
                'minor' => 1,
                'released_at' => '2012-03-27',
            ],
            [
                'major' => 3,
                'minor' => 0,
                'released_at' => '2012-02-22',
            ],
            [
                'major' => 2,
                'minor' => 0,
                'released_at' => '2011-09-01',
            ],
            [
                'major' => 1,
                'minor' => 0,
                'released_at' => '2011-06-01',
            ],
        ];
    }
}
