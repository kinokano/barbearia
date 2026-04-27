<?php
/**
 * Helper de Data/Hora
 */
class DateHelper
{
    /**
     * Retorna a data atual no formato Y-m-d
     */
    public static function today(): string
    {
        return date('Y-m-d');
    }

    /**
     * Retorna a hora atual no formato H:i:s
     */
    public static function now(): string
    {
        return date('H:i:s');
    }

    /**
     * Formata data para exibição: "18/04/2026"
     */
    public static function format(string $date, string $format = 'd/m/Y'): string
    {
        return date($format, strtotime($date));
    }

    /**
     * Verifica se a data é válida
     */
    public static function isValid(string $date, string $format = 'Y-m-d'): bool
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Verifica se a data está no futuro
     */
    public static function isFuture(string $date): bool
    {
        return $date > self::today();
    }

    /**
     * Verifica se datetime está no futuro (com horas)
     */
    public static function isDatetimeFuture(string $date, string $time): bool
    {
        $datetime = $date . ' ' . $time;
        return strtotime($datetime) > time();
    }

    /**
     * Retorna o nome do dia da semana
     */
    public static function dayOfWeek(string $date): string
    {
        $days = ['domingo', 'segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado'];
        return $days[date('w', strtotime($date))];
    }

    /**
     * Adiciona minutos a um horário
     */
    public static function addMinutes(string $time, int $minutes): string
    {
        return date('H:i:s', strtotime($time) + ($minutes * 60));
    }

    /**
     * Gera array de horários entre início e fim com intervalo
     */
    public static function generateTimeSlots(string $start, string $end, int $interval): array
    {
        $slots = [];
        $current = strtotime($start);
        $endTime = strtotime($end);

        while ($current < $endTime) {
            $slots[] = date('H:i', $current);
            $current += $interval * 60;
        }

        return $slots;
    }
}
