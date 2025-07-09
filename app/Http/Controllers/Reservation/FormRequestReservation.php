<?php

namespace App\Http\Controllers\Reservation;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\ResponseHelper\ResponseHelper;


class FormRequestReservation extends FormRequest
{
    use ResponseHelper;
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return match ($this->method())
        {
            'GET' => match ($this->route()->getActionMethod())
            {
                'show_all_reservation_for_table' => $this->show_all_reservation_for_table(),
                'get_nearest_reservation_info' => $this->get_nearest_reservation_info(),
            },
            'POST' => match ($this->route()->getActionMethod())
            {
                'create_reservation' => $this->create_reservation(),
                'check_in_reservation' => $this->cancel_reservation(),
                'extend_resservation_delay_time' => $this->extend_resservation_delay_time(),
                'extend_resservation' => $this->extend_resservation(),
            },

            'DELETE'=> match ($this->route()->getActionMethod())
            {
                'cancel_reservation' => $this->cancel_reservation(),
            },
            default => []
        };
    }

    public function extend_resservation_delay_time():array
    {
        return [
            'reservation_id' => 'required|integer'
        ];
    }

    public function get_nearest_reservation_info():array
    {
        return [
            'reservation_id' => 'required|integer'
        ];
    }

    public function extend_resservation():array
    {
        return [
            'reservation_id' => ['required', 'integer', 'exists:reservations,id'],
            'extended_until' => [
                'required',
                'date_format:Y-m-d H:i:s',
                function ($attribute, $value, $fail) {
                    $reservationId = request('reservation_id');

                    // تأكد أن الرقم صحيح و الحجز موجود
                    $reservation = \App\Models\Reservation::find($reservationId);

                    if (!$reservation) {
                        return $fail('The reservation does not exist.');
                    }

                    // تحقق أن extended_until أكبر من وقت نهاية الحجز
                    $endTime = $reservation->reservation_end_time;

                    if (strtotime($value) <= strtotime($endTime)) {
                        return $fail('The extended time must be after the original reservation end time: ' . $endTime);
                    }
                },
            ],
        ];
    }


    public function show_all_reservation_for_table():array
    {
        return [
            'table_id' => 'required|integer'
        ];
    }

    public function create_reservation():array
    {
        return [
            'table_id' => 'required|integer',
            'reservation_start_time' =>'required|date_format:Y-m-d H:i:s|after_or_equal:now',
            'reservation_end_time' =>'required|date_format:Y-m-d H:i:s|after:reservation_start_time',
        ];
    }

    public function cancel_reservation():array
    {
        return [
            'reservation_id' => 'required|integer'
        ];
    }




    protected function failedValidation(Validator $validator)
    {
        // الحصول على اللغة الحالية
        $language = app()->getLocale();

        // تحميل الرسائل بناءً على اللغة
        $messages = $validator->errors()->toArray();

        // تحويل الرسائل إلى اللغة المطلوبة
        $translatedMessages = [];
        foreach ($messages as $field => $messageArray) {
            foreach ($messageArray as $message) {
                $translatedMessages[$field][] = __($message, [], $language);
            }
        }

        // إرجاع الاستجابة مع الرسائل المترجمة
        throw new ValidationException($validator, $this->Validation([], $translatedMessages));
    }
}
