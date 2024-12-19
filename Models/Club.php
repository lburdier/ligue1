<?php

class Club {
    private int $id; // ID of the club
    private string $nom_club; // Name of the club
    private string $emplacement; // Location of the club
    private string $ligue_club; // League property

    public function __construct(string $nom_club = "", string $emplacement = "", string $ligue_club = "") {
        $this->nom_club = $nom_club; // Initialize club name
        $this->emplacement = $emplacement; // Initialize location
        $this->ligue_club = $ligue_club; // Initialize league
    }

    // Getter for club ID
    public function getId(): int {
        return $this->id;
    }

    // Getter for club name
    public function getNom(): string {
        return $this->nom_club;
    }

    // Getter for location
    public function getEmplacement(): string {
        return $this->emplacement;
    }

    // Getter for league
    public function getLigue(): string {
        return $this->ligue_club;
    }

    // Setter for club ID
    public function setId(int $id): void {
        $this->id = $id;
    }

    // Setter for club name
    public function setNom(string $nom_club): void {
        $this->nom_club = $nom_club;
    }

    // Setter for location
    public function setEmplacement(string $emplacement): void {
        $this->emplacement = $emplacement;
    }

    // Setter for league
    public function setLigue(string $ligue_club): void {
        $this->ligue_club = $ligue_club;
    }
}