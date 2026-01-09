<?php

namespace App\Http\Resources\Back\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class BlaneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Get original values from database for the date fields
        $originalDates = DB::table('blanes')
            ->where('id', $this->id)
            ->select(['start_date', 'expiration_date', 'heure_debut', 'heure_fin'])
            ->first();

        $data = parent::toArray($request);

        // Ensure vendor_id is included in response (already included via parent::toArray() if in fillable)
        // vendor_id is automatically included as it's in the model's fillable array

        // Replace the transformed date fields with original database values
        if ($originalDates) {
            $data['start_date'] = $originalDates->start_date;
            $data['expiration_date'] = $originalDates->expiration_date;
            $data['heure_debut'] = $originalDates->heure_debut;
            $data['heure_fin'] = $originalDates->heure_fin;
        }

        // Always include share_url with different formats based on visibility
        if (isset($data['visibility'])) {
            $frontendUrl = config('app.frontend_url', 'http://localhost:5173');

            switch ($data['visibility']) {
                case 'link':
                    // Share URL with token for link visibility
                    if (isset($data['share_token'])) {
                        $data['share_url'] = "{$frontendUrl}/blane/{$this->slug}/{$data['share_token']}";
                    } else {
                        $data['share_url'] = null;
                    }
                    break;

                case 'public':
                    // Direct URL for public visibility
                    $data['share_url'] = "{$frontendUrl}/blane/{$this->slug}";
                    break;

                case 'private':
                default:
                    // No shareable URL for private visibility
                    $data['share_url'] = null;
                    break;
            }
        }

        return $data;
    }
}
