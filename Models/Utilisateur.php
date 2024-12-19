<?php

class Utilisateur {

    public function __construct(
            int $id_uti,
            int $id_club,
            string $nom_uti,
            string $sexe_uti,
            string $password_uti,
            int $date_inscription, // Consider changing to a DateTime type if needed
            string $image_uti,
            string $mail_uti
    ) {
        $this->id_uti = $id_uti;
        $this->id_club = $id_club;
        $this->nom_uti = $nom_uti;
        $this->sexe_uti = $sexe_uti;
        $this->password_uti = $password_uti;
        $this->date_inscription = $date_inscription; // Use DateTime if needed
        $this->image_uti = $image_uti;
        $this->mail_uti = $mail_uti;
    }

    public function getId(): int {
        return $this->id_uti;
    }

    public function getIdClub(): int {
        return $this->id_club;
    }

    public function getNom(): string {
        return $this->nom_uti;
    }

    public function getSexe(): string {
        return $this->sexe_uti;
    }

    public function getPassword(): string {
        return $this->password_uti;
    }

    public function getDateInscription(): int { // Consider returning DateTime
        return $this->date_inscription; // Change to DateTime if needed
    }

    public function getImage(): string {
        return $this->image_uti;
    }

    public function getMail(): string {
        return $this->mail_uti;
    }

    public function setId(int $id_uti): void {
        $this->id_uti = $id_uti;
    }

    public function setIdClub(int $id_club): void {
        $this->id_club = $id_club;
    }

    public function setNom(string $nom_uti): void {
        $this->nom_uti = $nom_uti;
    }

    public function setSexe(string $sexe_uti): void {
        $this->sexe_uti = $sexe_uti;
    }

    public function setPassword(string $password_uti): void {
        $this->password_uti = $password_uti;
    }

    public function setDateInscription(int $date_inscription): void { // Change to DateTime if needed
        $this->date_inscription = $date_inscription; // Change to DateTime if needed
    }

    public function setImage(string $image_uti): void {
        $this->image_uti = $image_uti;
    }

    public function setMail(string $mail_uti): void {
        $this->mail_uti = $mail_uti;
    }
}
