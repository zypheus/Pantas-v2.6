<?php

namespace Database\Seeders;

use App\Models\Room;
use Illuminate\Database\Seeder;

class RoomSampleSeeder extends Seeder
{
    public function run(): void
    {
        $rooms = [
            [
                'name' => 'Discussion Room A',
                'description' => 'Small group discussion room near the reading area.',
                'capacity' => 6,
            ],
            [
                'name' => 'Discussion Room B',
                'description' => 'Collaborative room for project meetings and study sessions.',
                'capacity' => 8,
            ],
            [
                'name' => 'Multimedia Room',
                'description' => 'Room for presentations, media viewing, and group activities.',
                'capacity' => 15,
            ],
        ];

        foreach ($rooms as $room) {
            Room::query()->updateOrCreate(
                ['name' => $room['name']],
                $room,
            );
        }
    }
}
