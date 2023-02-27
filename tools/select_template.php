<?php
    $providersLabel = array_column($channel['available_providers'], 'label');
?>
<div class="multiselect">
    <div>
        <span>Services activés</span><br/>
        <select multiple class="select-list select1" id="select1_<?php echo $id; ?>" size="10">
        <?php
        if(isset($channel['priority'])) {
            foreach ($channel['priority'] as $key => $priority) {
                if (in_array($priority, $providersLabel)) {
                    echo '<option data-results="' . $key . '" value="' . htmlentities($priority) . '">' . htmlentities($priority) . '</option>';
                }
            }
        }
        ?>
    </select><br/>
    <span>Vide = Tous activés</span>
    </div>
    <div class="options" id="options_<?php echo $id; ?>">
        <input type="button" id="move-up" value="Up">
        <input type="button" id="move-down" value="Down">
        <input type="button" id="move-over" value="<- ->">
    </div>
    <div>
     <span>Services disponibles</span><br/>
    <select multiple class="select-list select2" id="select2_<?php echo $id; ?>" size="10">
        <?php
        foreach($providersLabel as $key => $providersLabel) {
            if(!isset($channel['priority']) || !in_array($providersLabel, $channel['priority'])) {
                echo '<option data-results="'.$key.'" value="'.htmlentities($providersLabel) . '">' . htmlentities($providersLabel) . '</option>';
            }
        }
        ?>
    </select>
    </div>
</div>