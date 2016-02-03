<?php if ( !defined('ABSPATH') ) exit; // Shhh   
	$cont = 1;   ?>

    <ul class="choices">
	<?php foreach ( get_list_choices() as $choice ): ?>
    	<li>
    		<div class="choice-content">
    			<?php if (strlen($choice->image)>0) { ?>
	    			<div class="wp-caption">
	    				<a href="<?php echo $choice->full; ?>">
	    					<img src="<?php echo $choice->image; ?>" />
	    				</a>
	    			</div>
    			<?php } ?>
    			<h3><?php echo $cont." - "; $cont++; echo $choice->title; ?></h3>
				<p><?php echo $choice->text; ?></p>
				<?php list_choice_vote_rendered($choice); ?>
    		</div>
    		<div class="input">
    			<span class='hasr'><?php _e('Haz recibido', LFA_TD); ?><b> <?php echo $choice->votes; ?> 
	    		<?php _e('puntos', LFA_TD); ?></b></span>
	    		<div class="inputswei">
	    			<?php if(ListForArticles()->security->has_ability_to_vote($choice->id)) { ?>
		    			<form method="post">
		    				<input type="hidden" name="lfa_list_id" value="<?php echo get_list_id(); ?>">
		    				<input type="hidden" name="lfa_choices" value="<?php echo $choice->id ?>" <?php disabled(false, display_list_buttons()); ?> />
			    			<?php _e('Vota', LFA_TD); ?>:
			    		    <input type="radio" name="lfa_votes" value="1" <?php disabled(false, display_list_buttons()); ?> /> 1 
			    		    <?php _e('puntos', LFA_TD); ?>
			    		    <input type="radio" name="lfa_votes" value="2" <?php disabled(false, display_list_buttons()); ?> /> 2 
			    		    <?php _e('puntos', LFA_TD); ?>
			    		    <input type="radio" name="lfa_votes" value="3" <?php disabled(false, display_list_buttons()); ?> /> 3 
			    		    <?php _e('puntos', LFA_TD); ?>
			    		    <input type="radio" name="lfa_votes" value="4" <?php disabled(false, display_list_buttons()); ?> /> 4 
			    		    <?php _e('puntos', LFA_TD); ?>
			    		    <input type="radio" name="lfa_votes" value="5" <?php disabled(false, display_list_buttons()); ?> /> 5 
			    		    <?php _e('puntos', LFA_TD); ?>
			    		    <button name="lfa_action" value="vote" class="lfa-primary-btn"><?php _e('Vote', LFA_TD); ?></button>
						</form>
						<div class="readymsg"><?php _e('El voto ha sido realizado', LFA_TD); ?></div>
					<?php } else { ?>
						<?php _e('El voto ha sido realizado', LFA_TD); ?>
					<?php } ?>
				</div>
    		</div>
    	</li>
	<?php endforeach; ?>
    </ul>