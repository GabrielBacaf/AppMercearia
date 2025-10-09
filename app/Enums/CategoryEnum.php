<?php

namespace App\Enums;

enum CategoryEnum: string

{
    // Groceries & Pantry Staples
    case GROCERIES = 'Mercearia';
    case GRAINS_AND_CEREALS = 'Grãos e Cereais';
    case PASTA_AND_SAUCES = 'Massas e Molhos';
    case OILS_AND_FATS = 'Óleos e Gorduras';
    case CANNED_GOODS = 'Enlatados e Conservas';
    case BREAKFAST_AND_COFFEE = 'Matinais e Café';

        // Fresh Produce
    case FRESH_PRODUCE = 'Hortifrúti';
    case FRUITS = 'Frutas';
    case VEGETABLES = 'Legumes e Verduras';

        // Butchery & Seafood
    case BUTCHERY = 'Açougue';
    case SEAFOOD = 'Peixaria';

        // Dairy & Cold Cuts
    case DAIRY_AND_COLD_CUTS = 'Frios e Laticínios';
    case MILK_AND_YOGURTS = 'Leites e Iogurtes';
    case CHEESES = 'Queijos';
    case COLD_CUTS = 'Embutidos';

        // Bakery & Confectionery
    case BAKERY_AND_CONFECTIONERY = 'Padaria e Confeitaria';
    case BREADS = 'Pães';
    case CAKES_AND_PIES = 'Bolos e Tortas';
    case COOKIES_AND_CRACKERS = 'Biscoitos e Torradas';

        // Frozen Foods
    case FROZEN_FOODS = 'Congelados';
    case FROZEN_MEALS = 'Pratos Prontos Congelados';
    case ICE_CREAM = 'Sorvetes';

        // Beverages
    case BEVERAGES = 'Bebidas';
    case WATER_AND_JUICES = 'Águas e Sucos';
    case SODAS = 'Refrigerantes';
   

        // Personal Hygiene
    case PERSONAL_HYGIENE = 'Higiene Pessoal';
    case HAIR_CARE = 'Cuidados com o Cabelo';
    case ORAL_HYGIENE = 'Higiene Bucal';

        // Cleaning Supplies
    case CLEANING_SUPPLIES = 'Limpeza';
    case LAUNDRY = 'Limpeza de Roupas';
    case HOUSEHOLD_CLEANING = 'Limpeza da Casa';

        // Special Categories
    case PET_SUPPLIES = 'Pet Shop';
    case HOUSEHOLD_UTILITIES = 'Utilidades Domésticas';
    case OTHERS = 'Outros';


    public function label(): string
    {
        return $this->value;
    }


    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
