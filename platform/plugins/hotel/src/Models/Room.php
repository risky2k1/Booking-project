<?php

namespace Botble\Hotel\Models;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;

class Room extends BaseModel
{
    protected $table = 'ht_rooms';

    protected $fillable = [
        'name',
        'description',
        'content',
        'is_featured',
        'images',
        'price',
        'currency_id',
        'number_of_rooms',
        'number_of_beds',
        'size',
        'max_adults',
        'max_children',
        'room_category_id',
        'tax_id',
        'order',
        'status',
    ];

    protected $casts = [
        'status' => BaseStatusEnum::class,
    ];

    public function getImagesAttribute($value)
    {
        if ($value === '[null]') {
            return [];
        }

        $images = json_decode((string)$value, true);

        if (is_array($images)) {
            $images = array_filter($images);
        }

        return $images ?: [];
    }

    public function getImageAttribute(): ?string
    {
        return Arr::first($this->images) ?? null;
    }

    public function amenities(): BelongsToMany
    {
        return $this->belongsToMany(Amenity::class, 'ht_rooms_amenities', 'room_id', 'amenity_id');
    }

    public function currency(): BelongsTo
    {
        return $this->belongsTo(Currency::class, 'currency_id')->withDefault();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(RoomCategory::class, 'room_category_id')->withDefault();
    }

    public function isAvailableAt(array $filters = []): bool
    {
        if (empty($filters['start_date']) || empty($filters['end_date'])) {
            return true;
        }

        $roomDates = $this->activeRoomDates;
        $allDates = [];
        for (
            $index = strtotime($filters['start_date']); $index < strtotime(
                $filters['end_date']
            ); $index += 60 * 60 * 24
        ) {
            $allDates[date('Y-m-d', $index)] = [
                'number' => $this->number_of_rooms,
                'price' => $this->price,
            ];
        }

        if ($roomDates->count()) {
            foreach ($roomDates as $row) {
                if (! $row->active || ! $row->number_of_rooms || ! $row->price) {
                    return false;
                }

                if (! array_key_exists(date('Y-m-d', strtotime($row->start_date)), $allDates)) {
                    continue;
                }

                $allDates[date('Y-m-d', strtotime($row->start_date))] = [
                    'number' => $row->number_of_rooms,
                    'price' => $row->price,
                ];
            }
        }

        $roomBookings = $this->activeBookingRooms;

        if (! empty($roomBookings)) {
            foreach ($roomBookings as $roomBooking) {
                for (
                    $index = strtotime($roomBooking->start_date); $index < strtotime(
                        $roomBooking->end_date
                    ); $index += 60 * 60 * 24
                ) {
                    if (! array_key_exists(date('Y-m-d', $index), $allDates)) {
                        continue;
                    }

                    $allDates[date('Y-m-d', $index)]['number'] -= $roomBooking->number_of_rooms;

                    if ($allDates[date('Y-m-d', $index)]['number'] <= 0) {
                        return false;
                    }
                }
            }
        }

        $allDates = array_column($allDates, 'number');

        $maxNumberPerDay = 0;
        if ($allDates) {
            $maxNumberPerDay = (int)min($allDates);
        }

        if (empty($maxNumberPerDay)) {
            return false;
        }

        if (! empty($filters['adults']) && $this->max_adults < $filters['adults']) {
            return false;
        }

        if (! empty($filters['children']) && $this->max_children < $filters['children']) {
            return false;
        }

        return true;
    }

    public function getRoomTotalPrice(string $startDate, string $endDate, int $nights = 1): float|int
    {
        $roomDates = $this->activeRoomDates;

        $originalPrice = $this->price * $nights;

        if (! $roomDates->count()) {
            return $originalPrice;
        }

        $allDates = [];
        for ($index = strtotime($startDate); $index < strtotime($endDate); $index += 60 * 60 * 24) {
            $allDates[date('Y-m-d', $index)] = [
                'number' => $this->number_of_rooms,
                'price' => $this->price,
            ];
        }

        if (! empty($roomDates)) {
            $price = 0;
            $specialPriceCount = 0;
            foreach ($roomDates as $row) {
                if (! $row->active || ! $row->number_of_rooms || ! $row->price) {
                    continue;
                }

                if (! array_key_exists(date('Y-m-d', strtotime($row->start_date)), $allDates)) {
                    continue;
                }

                $price += $row->price;
                $specialPriceCount++;
            }

            return $price + ($nights - $specialPriceCount) * $this->price;
        }

        return $originalPrice;
    }

    public function activeRoomDates(): HasMany
    {
        return $this->hasMany(RoomDate::class, 'room_id');
    }

    public function activeBookingRooms(): HasMany
    {
        return $this
            ->hasMany(BookingRoom::class, 'room_id')
            ->active();
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class, 'tax_id')->withDefault();
    }
}
