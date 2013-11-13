<?php
namespace PhroznPlugin\Provider;

class Primarchs
    extends \Phrozn\Provider\Base
    implements \Phrozn\Provider 
{
    private $primarchs = array(
        array("Lion El'Jonson",           "Dark Angels",                  "Loyal"),
        array("Fulgrim",                  "Emperor's Children",           "Traitor"),
        array("Perturabo",                "Iron Warriors",                "Traitor"),
        array("Jaghatai Khan",            "White Scars",                  "Loyal"),
        array("Leman Russ",               "Space Wolves",                 "Loyal"),
        array("Rogal Dorn",               "Imperial Fists",               "Loyal"),
        array("Night Haunter",            "Night Lords",                  "Traitor"),
        array("Sanguinius",               "Blood Angels",                 "Loyal"),
        array("Ferrus Manus",             "Iron Hands",                   "Loyal"),
        array("Angron",                   "World Eaters",                 "Traitor"),
        array("Roboute Guilliman",        "Ultramarines",                 "Loyal"),
        array("Mortarion",                "Death Guard",                  "Traitor"),
        array("Magnus the Red",           "Thousand Sons",                "Traitor"),
        array("Horus",                    "Luna Wolves/Sons of Horus",    "Traitor"),
        array("Lorgar",                   "Word Bearers",                 "Traitor"),
        array("Vulkan",                   "Salamanders",                  "Loyal"),
        array("Corax",                    "Raven Guard",                  "Loyal"),
        array("Alpharius1 & Omegeon2",    "Alpha Legion",                 "Complicated"),
    );

    public function get()
    {
        // get reference to configuration object (it holds passed vars, if any)
        $config = $this->getConfig();

        // form list, replacing numeric keys with associative 
        // you can get rid of this by updating original array
        $primarchs = array_map(function ($item) { 
            return array(
                'name'          => $item[0],
                'legion'        => $item[1],
                'allegiance'    => $item[2],
            );
        }, $this->primarchs);

        // apply allegiance filter
        if (isset($config['allegiance']) && $allegiance = $config['allegiance']) {
            $primarchs = array_filter($primarchs, function ($primarch) use ($allegiance) {
                return $allegiance === $primarch['allegiance'];
            });
        }

        return $primarchs;
    }
}
