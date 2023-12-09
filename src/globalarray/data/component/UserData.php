<?php

declare(strict_types=1);

namespace globalarray\data\component;

final class UserData{

    /** @var string */
    private $displayedName;

    public function __construct(
        string $displayedName
    ) {
        $this->displayedName = $displayedName;
    }

    public function getDisplayedName() : string{
        return $this->displayedName;
    }

    public function setDisplayedName(string $name) : void{
        $this->displayedName = $name;
    }
}