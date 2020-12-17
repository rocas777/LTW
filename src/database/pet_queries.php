<?php
include_once('../database/connection.php');

function get_pets_filtered($name, $species, $size, $color, $location, $state, $user, $first_elem, $length){
    $name_sql = '((? is NULL) <> (NULL is not NULL))';
    $species_sql = '((? is NULL) <> (NULL is not NULL))';
    $size_sql = '((? is NULL) <> (NULL is not NULL))';
    $color_sql = '((? is NULL) <> (NULL is not NULL))';
    $location_sql = '((? is NULL) <> (NULL is not NULL))';
    $state_sql = '((? is NULL) <> (NULL is not NULL))';
    $user_sql = '((? is NULL) <> (NULL is not NULL))';
    if($name!=null){
        $name_sql = '(name = ?)';
    }
    if($size!=null){
        $size_sql = '(size = ?)';
    }
    if($color!=null){
        $color_sql = '(color = ?)';
    }
    if($location!=null){
        $location_sql = '(location = ?)';
    }
    global $dbh;
    $stmt = $dbh->prepare('SELECT petId,name,size,color,location,user from Pets 
        where '.$name_sql.' and '.$size_sql.' and '.$color_sql.' and '.$location_sql.' and '.$user_sql.' LIMIT ? OFFSET ?');
    $stmt->execute(array($name,$size,$color,$location,$user,$length,$first_elem));
    return $stmt->fetchAll();
}

function get_pets(){
    global $dbh;
    $stmt = $dbh->prepare('SELECT petId,Pets.name,size,color,location,PetState.state,path,gender, Species.specie, Users.userName FROM Pets JOIN Users ON Pets.user=Users.userId JOIN Species ON Pets.species = Species.specieId JOIN PetState ON Pets.state = PetState.petStetId JOIN Photos ON Pets.profilePic=Photos.photoId');
    $stmt->execute();
    return $stmt->fetchAll();
}

function get_pet_photo($pet_id){
    global $dbh;
    $stmt = $dbh->prepare('SELECT path from Photos join Pets on Photos.photoId = Pets.profilePic where pet = ? ORDER BY path DESC');
    $stmt->execute(array($pet_id));
    return $stmt->fetchAll()[0]["path"];
}

function get_species(){
    global $dbh;
    $stmt = $dbh->prepare('SELECT * from Species ');
    $stmt->execute(array());
    return $stmt->fetchAll();
}

function get_states(){
    global $dbh;
    $stmt = $dbh->prepare('SELECT state from PetState ');
    $stmt->execute(array());
    return $stmt->fetchAll();
}

function get_specie_id($specie_name){
    global $dbh;
    $stmt = $dbh->prepare('SELECT specieId from Species where specie=?');
    $stmt->execute(array($specie_name));
    return $stmt->fetchAll()[0]['specieId'];
}

function get_specie($specie_id){
    global $dbh;
    $stmt = $dbh->prepare('SELECT * from Species where specieId=?');
    $stmt->execute(array($specie_id));
    return $stmt->fetchAll()[0];
}

function get_color_id($color){
    global $dbh;
    $stmt = $dbh->prepare('SELECT color from Colors where color=?');
    $stmt->execute(array($color));
    return $stmt->fetchAll()[0]['color'];
}

function get_colors(){
    global $dbh;
    $stmt = $dbh->prepare('SELECT color from Colors ');
    $stmt->execute(array());
    return $stmt->fetchAll();
}

function get_pet_data($pet_id){
    global $dbh;
    $stmt = $dbh->prepare('SELECT * from Pets where petId=?');
    $stmt->execute(array($pet_id));
    return $stmt->fetchAll()[0];
}

function get_pet_photos($pet_id){
    global $dbh;
    $stmt = $dbh->prepare('SELECT path from Photos where pet=?');
    $stmt->execute(array($pet_id));
    return $stmt->fetchAll();
}

function get_state_description($state){
    global $dbh;
    $stmt = $dbh->prepare('SELECT state from PetState where petStetId=?');
    $stmt->execute(array($state));
    return $stmt->fetchAll()[0];
}

function add_pet($name,$species,$size,$color,$location,$state,$user,$profilePic,$gender){
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Pets(name, species,size, color,location,state,user,profilePic,gender) VALUES(?,?,?,?,?,?,?,?,?)');
    return $stmt->execute(array($name,$species,$size,$color,$location,$state,$user,$profilePic,$gender));
}
function edit_pet($name,$species,$size,$color,$location,$state,$gender,$pet_id){
    global $dbh;
    $stmt = $dbh->prepare('UPDATE Pets set name=?, species=?,size=?, color=?,location=?,state=?,gender=? where petId=?');
    return $stmt->execute(array($name,$species,$size,$color,$location,$state,$gender,$pet_id));
}

function get_pet($pet_name){
    global $dbh;
    $stmt = $dbh->prepare('SELECT * from Pets where name=?');
    $stmt->execute(array($pet_name));
    return $stmt->fetchAll();
}

function add_pet_photo_to_db($photo_path, $pet_id){
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Photos(path,pet) VALUES(?,?)');
    $stmt->execute(array($photo_path,$pet_id));

    $stmt = $dbh->prepare('SELECT photoid from Photos where path=?');
    $stmt->execute(array($photo_path));
    return $stmt->fetchAll()[0]['photoId'];
}

function change_pet_photo_id($pet_id, $photo_id){
    global $dbh;
    $stmt = $dbh->prepare('UPDATE Pets SET profilePic = ? WHERE petId = ?;');
    return $stmt->execute(array($photo_id,$pet_id));
}

function check_pet($pet_id) {
    global $dbh;

    $stmt = $dbh->prepare('SELECT * FROM Pets WHERE petId = ?');
    $stmt->execute(array($pet_id));
    $length = count($stmt->fetchAll());
    return $length>0;
}

function check_pet_user_association($user_name, $pet_id) {
    global $dbh;
    $stmt = $dbh->prepare('SELECT * FROM Favourites WHERE user = ? AND pet = ?');
    $stmt->execute(array($user_name,$pet_id));
    $length = count($stmt->fetchAll());
    return $length>0;
}

function add_pet_favourite($user_name, $pet_id){
    global $dbh;
    $stmt = $dbh->prepare('Insert into Favourites(user,pet) values (?,?);');
    $stmt->execute(array($user_name,$pet_id));
}

function remove_pet_favourite($user_name, $pet_id){

    global $dbh;
    $stmt = $dbh->prepare('DELETE FROM Favourites WHERE user = ? AND pet = ?;');
    $stmt->execute(array($user_name,$pet_id));
}


function get_pet_questions($pet_id){
    global $dbh;
    $stmt = $dbh->prepare('SELECT questionId, userName, date, questionTxt FROM Questions JOIN Users ON Questions.user = Users.userId WHERE pet = ?');
    $stmt->execute(array($pet_id));

    return $stmt->fetchAll();
}


function add_question($pet_id, $user_id, $comment_text, $date){
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Questions (questionTxt,pet,date,user) VALUES (?,?,?,?)');
    $stmt->execute(array($comment_text,$pet_id,$date,$user_id));
}


function show_question_reply($question_id){
    global $dbh;
    $stmt = $dbh->prepare('SELECT answerId, userName, Questions.date, answerTxt, Answers.question FROM ((Answers JOIN Users ON Answers.author = Users.userId) JOIN Questions ON Questions.questionId = Answers.question) WHERE Questions.questionId = ?');
    $stmt->execute(array($question_id));

    return $stmt->fetchAll();
}

function add_question_reply($answer_txt, $question, $date, $author){
    global $dbh;
    $stmt = $dbh->prepare('INSERT INTO Answers (answerTxt,question,date,author) VALUES (?,?,?,?)');
    $stmt->execute(array($answer_txt,$question,$date,$author));
}

function accepted_proposals($pet_id){
    global $dbh;
    $stmt = $dbh->prepare('select * from Proposals where pet=? and state=1');
    $stmt->execute(array($pet_id));
    return count($stmt->fetchAll());
}

function remove_pet($pet_id){
    global $dbh;
    $stmt1 = $dbh->prepare('DELETE from Pets where petId=?');
    $stmt1 ->execute(array($pet_id));
    $stmt2 = $dbh->prepare('DELETE from Photos where pet=?');
    $stmt2 ->execute(array($pet_id));
    $questions = $dbh->prepare('Select * from Questions where pet=?');
    $questions ->execute(array($pet_id));

    $del_answer = $dbh->prepare('DELETE from Answers where question=?');
    foreach ($questions as $question){
        $del_answer->execute(array($question['questionId']));
    }

    $stmt4 = $dbh->prepare('DELETE from Questions where pet=?');
    $stmt4 ->execute(array($pet_id));

    $stmt4 = $dbh->prepare('DELETE from Proposals where pet=?');
    $stmt4 ->execute(array($pet_id));
}

function update_state($pet_id,$state){
    global $dbh;
    $stmt = $dbh->prepare('UPDATE Pets SET state=? WHERE petId=?');
    $stmt->execute(array($state,$pet_id));
}