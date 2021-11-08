<?php

namespace App\Classes;

class PriceHelper
{
    /*
     * Todo: Coding Test for Technical Hires
     * Please read the instructions on the README.md
     * Your task is to write the functions for the PriceHelper class
     * A set of sample test cases and expected results can be found in PriceHelperTest
     */

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the unit price of an item based on the quantity.
     *
     * Question:
     * If I purchase 10,000 bicycles, the unit price of the 10,000th bicycle would be 1.50
     * If I purchase 10,001 bicycles, the unit price of the 10,001st bicycle would be 1.00
     * If I purchase 100,001 bicycles, what would be the unit price of the 100,001st bicycle?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getUnitPriceTierAtQty(int $qty, array $tiers): float
    {
        // non-numberic keys could break this. Removing non-numric keys in the tiers array
        $filteredTiers = array_filter($tiers, function ($k) { return is_numeric($k); }, ARRAY_FILTER_USE_KEY);
        // this method only works if tier is sorted by lowest to highest by default.
        ksort($filteredTiers);

        if ($qty == 0) return 0.0;

        // foreach ($filteredTiers as $key => $value)
        for ($i = 0; $i < count($filteredTiers); $i++)
        {
            // if by the time reach the last tier, it has passed all tiers. use last value.
            if ($i == count($filteredTiers)-1) return array_values($filteredTiers)[$i];
            $nextKey = array_keys($filteredTiers)[$i+1];
            if ($qty < $nextKey) return array_values($filteredTiers)[$i];
        }

        // returns base price
        return array_values($filteredTiers)[0];
    }

    /**
     * Task: Given an associative array of minimum order quantities and their respective prices, write a function to return the total price of an order of items based on the quantity ordered
     *
     * Question:
     * If I purchase 10,000 bicycles, the total price would be 1.5 * 10,000 = $15,000
     * If I purchase 10,001 bicycles, the total price would be (1.5 * 10,000) + (1 * 1) = $15,001
     * If I purchase 100,001 bicycles, what would the total price be?
     *
     * @param int $qty
     * @param array $tiers
     * @return float
     */
    public static function getTotalPriceTierAtQty(int $qty, array $tiers): float
    {
        $unitPrice = self::getUnitPriceTierAtQty($qty,$tiers);
        return $unitPrice*$qty;
    }

    /**
     * Task: Given an array of quantity of items ordered per month and an associative array of minimum order quantities and their respective prices, write a function to return an array of total charges incurred per month. Each item in the array should reflect the total amount the user has to pay for that month.
     *
     * Question A:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on a special pricing tier where the quantity does not reset each month and is thus CUMULATIVE.
     *
     * Question B:
     * A user purchased 933, 22012, 24791 and 15553 bicycles respectively in Jan, Feb, Mar, April
     * The management would like to know how much to bill this user for each of those month.
     * This user is on the typical pricing tier where the quantity RESETS each month and is thus NOT CUMULATIVE.
     *
     */
    public static function getPriceAtEachQty(array $qtyArr, array $tiers, bool $cumulative = false): array
    {
        $totalQty = 0;
        $monthlyTotal = [];
        foreach ($qtyArr as $key => $qty)
        {
            if ($cumulative) $totalQty += $qty;
            else $totalQty = $qty;
            // use back the same key. So if the array of qty is in assoc array, it returns the month's name, else it just uses back the index.
            $monthlyTotal[$key] = self::getTotalPriceTierAtQty($totalQty,$tiers);
        }
        return $monthlyTotal;
    }
}
