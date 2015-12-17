<?php
						/*
						  display finish button or giveme button
						  if watching user doesn't log in, button is not shown
						 */
						?>

						<!-- when login user is author -->
						<?php if($user_ID == $authordata->ID){
								if(isFinish($post->ID)){
									if(isBidderEvaluated($post->ID)){ ?>
							<!-- when status is finish -->
									この商品は評価済です。
								<?php }else{ ?>

								<div id="evaluation">
									落札者の評価:</br>
									<select name="score" id="score">
										<option value="invalid" selected>--選択--</option>
										<option value="5" >とても良い</option>
										<option value="4" >良い</option>
										<option value="3" >普通</option>
										<option value="2" >悪い</option>
										<option value="1" >とても悪い</option>
									</select>
									</br>
									コメント(任意 100字以内)</br>
									<textarea name="trade_comment" id="trade_comment" rows="5" cols="40"></textarea></br>
									<input type="button" id="evaluation" value="評価する" onClick="onBidderEvaluation();">
								</div>

								<?php } ?>
							<?php }elseif(isConfirm($post->ID)){ ?>
							<!-- when status is confirm -->

						<input type="button" id="finish" value="取引完了" onClick="onFinish();">
							<?php }elseif(isGiveme($post->ID)){ ?>
							<!-- when status is giveme -->
													<a href="<?php echo get_giveme_from_others_url(); ?>">取引相手を確定させてください。</a>
							<?php }else{ ?>
									この商品は「ください」待ちです。<br>

									<!-- when login user is not author -->
						<?php }elseif(!is_user_logged_in()){?>
						<!-- information is hidden for un-login users -->
						<?php }elseif(isConfirm($post->ID)){
								if(get_bidder_id($post->ID) == $user_ID){
									if(isExhibiterEvaluated($post->ID)){ ?>
										この商品は評価済です。
								<?php }elseif(isFinish($post->ID)){ ?>
								<div id="evaluation">
									出品者の評価:</br>
									<select name="score" id="score">
										<option value="invalid" selected>--選択--</option>
										<option value="5" >とても良い</option>
										<option value="4" >良い</option>
										<option value="3" >普通</option>
										<option value="2" >悪い</option>
										<option value="1" >とても悪い</option>
									</select>
									</br>
									コメント(任意 100字以内 改行も1文字と数えます)</br>
									<textarea name="trade_comment" id="trade_comment" rows="5" cols="40"></textarea></br>
									<input type="button" id="evaluation" value="評価する" onClick="onExhibiterEvaluation();">
								</div>
								<?php }else{ ?>
									取引完了待ちです。
								<?php } ?>
							<?php }else{ ?>
									この商品は取引相手が決まったため、「ください」はできません
							<?php } ?>
						<?php }elseif(is_user_logged_in()){
								if(doneGiveme($post->ID, $user_ID)){ ?>
						<!--input type="button" id="cancelGiveme" value="ください取消" onClick="onCancelGiveme();"-->
							<?php }elseif(get_usable_point($user_ID) > 0){ ?>
						<!--input type="button" id="giveme" value="ください" onClick="onGiveme();"-->
							<?php }else{ ?>
						使用可能なポイントが無いため「ください」できません。
							<?php } ?>
						<?php } ?>