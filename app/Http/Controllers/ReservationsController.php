<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use DB;
use App\Models\Reservation;


class ReservationsController extends Controller
{
    public function index()
    {
        $userId = auth()->user()->id;
        $today = today();

        $activityList = Db::Table('activities')
            ->get();

        $range = date('Y-m-d', strtotime(today() . ' + 15 days')) . ' 00:00:00';

        $checkdatesAr3 = array(null);

        $reservedPlaces = Db::Table('activities')
            ->join('timetables', 'timetables.activity_id', '=', 'activities.id')
            ->join('reservations', 'reservations.timetable_id', '=', 'timetables.id')
            ->whereBetween('reservations.reservationDay', [$today, $range])
            ->select('activities.name', 'reservations.user_id', 'reservations.reservationDay', 'timetables.start', 'timetables.finish')
            ->get();

        if (count($reservedPlaces) != 0) {

            for ($i = 0; $i <= 14; $i++) {

                $dayOfWeekPlus = date('Y-m-d', strtotime($today . ' +' . $i . 'days'));
                $dayOfWeek = date("N", strtotime($dayOfWeekPlus));

                $timetablesPerDay = Db::Table('timetables')
                    ->where('dayOfTheWeek', $dayOfWeek)
                    ->get();

                foreach ($timetablesPerDay as $index) {

                    $checkDay = $dayOfWeekPlus . ' ' . $index->start;
                    $hour = $index->start;
                    $hourR = Db::Table('timetables')
                        ->join('reservations', 'timetables.id', '=', 'reservations.timetable_id')
                        ->where('timetables.dayOfTheWeek', '=', $dayOfWeek)
                        ->where('timetables.activity_id', '=', 'first')
                        ->where('timetables.start', '=', $hour)
                        ->where('reservations.reservationDay', '=', $dayOfWeekPlus)
                        ->get()
                        ->count();

                    $hourRU = Db::Table('timetables')
                        ->join('reservations', 'timetables.id', '=', 'reservations.timetable_id')
                        ->where('timetables.dayOfTheWeek', '=', $dayOfWeek)
                        ->where('timetables.activity_id', '=', 'first')
                        ->where('timetables.start', '=', $hour)
                        ->where('reservations.reservationDay', '=', $dayOfWeekPlus)
                        ->where('reservations.user_id', '=', $userId)
                        ->get()
                        ->count();

                    $checkedDates = [$checkDay, $hourR, $hourRU];

                    $checkdatesAr1 = $this->result($checkedDates);

                    $checkdatesAr4 = array();
                    $arr = array();
                    $arr['Hora'] = $hour;
                    $arr['Estado'] = $checkdatesAr1[1];
                    $arr['Información'] = $checkdatesAr1[0];
                    $checkdatesAr4[] = $arr;

                    $checkdatesAr3[$dayOfWeekPlus] = $checkdatesAr4;
                }
            }
        }

        $checkdatesAr3 = array_map(function ($array) {
            return array((object)$array);
        }, $checkdatesAr3);

        $activityId = null;

        return view('reservations.index', compact('checkdatesAr3', 'activityList', 'activityId'));
    }


    public function showReservations(Request $request)
    {
        $userId = auth()->user()->id;
        $today = today();

        $activityId = $request->input('activityChosen');
        $activityList = Db::Table('activities')
            ->get();

        $range = date('Y-m-d', strtotime(today() . ' + 15 days')) . ' 00:00:00';

        $checkdatesAr3 = array(null);

        $reservedPlaces = Db::Table('activities')
            ->join('timetables', 'timetables.activity_id', '=', 'activities.id')
            ->join('reservations', 'reservations.timetable_id', '=', 'timetables.id')
            ->whereBetween('reservations.reservationDay', [$today, $range])
            ->select('activities.name', 'reservations.user_id', 'reservations.reservationDay', 'timetables.start', 'timetables.finish')
            ->get();

        if (count($reservedPlaces) != 0) {

            for ($i = 0; $i <= 14; $i++) {

                $dayOfWeekPlus = date('Y-m-d', strtotime($today . ' +' . $i . 'days'));
                $dayOfWeek = date("N", strtotime($dayOfWeekPlus));

                $timetablesPerDay = Db::Table('timetables')
                    ->where('dayOfTheWeek', $dayOfWeek)
                    ->get();

                foreach ($timetablesPerDay as $index) {

                    $checkDay = $dayOfWeekPlus . ' ' . $index->start;
                    $hour = $index->start;
                    $hourR = Db::Table('timetables')
                        ->join('reservations', 'timetables.id', '=', 'reservations.timetable_id')
                        ->where('timetables.dayOfTheWeek', '=', $dayOfWeek)
                        ->where('timetables.activity_id', '=', $activityId)
                        ->where('timetables.start', '=', $hour)
                        ->where('reservations.reservationDay', '=', $dayOfWeekPlus)
                        ->get()
                        ->count();

                    $hourRU = Db::Table('timetables')
                        ->join('reservations', 'timetables.id', '=', 'reservations.timetable_id')
                        ->where('timetables.dayOfTheWeek', '=', $dayOfWeek)
                        ->where('timetables.activity_id', '=', $activityId)
                        ->where('timetables.start', '=', $hour)
                        ->where('reservations.reservationDay', '=', $dayOfWeekPlus)
                        ->where('reservations.user_id', '=', $userId)
                        ->get()
                        ->count();

                    $checkedDates = [$checkDay, $hourR, $hourRU, $hour];

                    $checkdatesAr1 = $this->result($checkedDates);

                    $checkdatesAr4 = array();
                    $arr = array();
                    $arr['Hora'] = $hour;
                    $arr['Estado'] = $checkdatesAr1[1];
                    $arr['Información'] = $checkdatesAr1[0];
                    $checkdatesAr4[] = $arr;

                    $checkdatesAr3[$dayOfWeekPlus] = $checkdatesAr4;
                }
            }
        }

        $checkdatesAr3 = array_map(function ($array) {
            return array((object)$array);
        }, $checkdatesAr3);

        return view('reservations.index', compact('checkdatesAr3', 'activityId', 'activityList'));
    }

    public function result($checkedDates)
    {
        $state = "null";

        $reservationId = Db::Table('timetables')
            ->join('reservations', 'timetables.id', '=', 'reservations.timetable_id')
            ->where('timetables.start', '=', $checkedDates[3])
            ->where('reservations.reservationDay', '=', $checkedDates[0])
            ->where('reservations.user_id', '=', auth()->user()->id)
            ->select('reservations.id')
            ->value('id');

        if ($checkedDates[2] == 1) {
            $checkdatesAr = "<div class='border py-3 ps-3 pe-3'><p>Ya has reservado para ésta hora, quieres cancelar? " . "</p>" . "<div class='d-flex justify-content-center'><div><button class='ms-3'><a class='text-dark text-decoration-none' href=' . url('/reservations/cancel/' . $reservationId) . '>Cancelar</a></button></div></div></div>";
            $state = "Reservado";
        } else if ($checkedDates[1] == 0) {
            $checkdatesAr = "<div class='border py-3 ps-3 pe-3'><p>Hay plazas ésta hora, quieres reservar?" . "</p>" . "<form method='POST' action='/reservations/reserve/'. $reservationId enctype='multipart/form-data' style='margin-bottom:0px'>
            <input name='_token' type='hidden' value='" . csrf_token() . "'>
            <input type='hidden' name='reservationDate' value='$checkedDates[0]'>
            <div class='d-flex justify-content-center'><div><button type='submit'>Reservar</button></div></div>
            </form></div>";
            $state = "Hay plazas";
        } else if ($checkedDates[1] >= 1 && $checkedDates[1] <= 9 && $checkedDates[2] == 0) {
            $checkdatesAr = "<div class='border py-3 ps-3 pe-3'><p>Quedan " . (10 - $checkedDates[1]) . " plazas, quieres reservar?"  . "</p>" . "<form method='POST' action='/reservations/reserve/'. $reservationId enctype='multipart/form-data' style='margin-bottom:0px'>
            <input name='_token' type='hidden' value='" . csrf_token() . "'>
            <input type='hidden' name='reservationDay' value='$checkedDates[0]'>
            <div class='d-flex justify-content-center'><div><button type='submit'>Reservar</button></div></div>
            </form></div>";
            $state = "Hay plazas";
        } else if ($checkedDates[1] == 10) {
            $checkdatesAr = "<div class='border py-3 ps-3 pe-3'><p>Todas las plazas están ocupadas, losiento</p></div>";
            $state = "Sin plazas";
        }

        return [$checkdatesAr, $state];
    }

    public function reserve(Request $request)
    {
        if (
            Db::Table('reservations')
            ->where('reservationDay', $request->input('reservationDay'))
            ->where('user_id', auth()->user()->id)
            ->get()
            ->count() == 0
        ) {

            $reservation = new Reservation();
            $reservation->user_id = auth()->user()->id;
            $reservation->reservationDay = $request->input('reservationDay');
            $reservation->save();

            echo "<script>";
            echo "alert('La plaza se ha reservado con éxito');";
            echo "</script>";
        } else {

            echo "<script>";
            echo "alert('Ya tienes hecha una reserva para ése día');";
            echo "</script>";
        }

        $checkdatesAr3 = null;

        return redirect()->back()->with('checkdatesAr3');
    }

    public function cancel($matchdate)
    {
        Db::Table('reservations')
            ->where('reservationDay', $matchdate)
            ->where('user_id', auth()->user()->id)
            ->delete();

        echo "<script>";
        echo "alert('La plaza se ha cancelado con éxito');";
        echo "</script>";

        $checkdatesAr3 = null;

        return redirect()->back()->with('checkdatesAr3');
    }
}
