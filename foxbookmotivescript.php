<?php
$setMotive = $_COOKIE['color-motive'];
if($setMotive == "forest" || !isset($setMotive)){
        echo "<form method='post' id='motiveStyle'>
             <select name='motiveSelect' id='motiveSelect' onchange='this.form.submit()'>
             <option value='forest' disabled selected>Forest</option>
                <option value='arctic'>Arctic</option>
                <option value='night'>Night</option>
            </select>
            </form>";
    }else if($setMotive == "arctic"){
        echo "<form method='post' id='motiveStyle' action=''>
             <select name='motiveSelect' id='motiveSelect' onchange='this.form.submit()'>
             <option value='forest'>Forest</option>
                <option value='arctic' disabled selected>Arctic</option>
                <option value='night'>Night</option>
            </select>
            </form>";
    }else if($setMotive == "night"){
        echo "<form method='post' id='motiveStyle' action=''>
             <select name='motiveSelect' id='motiveSelect' onchange='this.form.submit()'>
             <option value='forest'>Forest</option>
                <option value='arctic'>Arctic</option>
                <option value='night' disabled selected>Night</option>
            </select>
            </form>";
    }else {
        echo "<form method='post' id='motiveStyle' action=''>
             <select name='motiveSelect' id='motiveSelect' onchange='this.form.submit()'>
             <option value='forest'>Forest</option>
                <option value='arctic'>Arctic</option>
                <option value='night'>Night</option>
            </select>
            </form>";
    }
if($_SERVER['REQUEST_METHOD'] == 'POST' && @$_POST['motiveSelect'] != ""){
    $motive = @$_POST['motiveSelect'];
    echo "<script>
            document.cookie = 'color-motive=$motive';
            </script>";
}
?>

