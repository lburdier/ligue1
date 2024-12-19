<?php

/*
 * Click nbfs://nbhost/SystemFileSystem/Templates/Licenses/license-default.txt to change this license
 * Click nbfs://nbhost/SystemFileSystem/Templates/Scripting/PHPClass.php to edit this template
 */

/**
 * Description of GestionAbonner
 *
 * @author UTI302
 */
class GestionAbonner {

    public function subscribeUser($userId, $clubId) {
        $stmt = $this->cnx->prepare("INSERT INTO s_abonner (user_id, club_id) VALUES (:user_id, :club_id)");
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':club_id', $clubId);
        return $stmt->execute();
    }
}
