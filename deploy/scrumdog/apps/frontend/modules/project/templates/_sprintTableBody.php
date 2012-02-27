    <tbody id="sprints_body">
    <?php $i=1; foreach($sprints as $sprint): ?>
      <tr<?php if($i%2==0):?> class="alt"<?php endif;?>>
        <td><a href="<?php echo(url_for('@sprint_manage?sprint_id='.$sprint->getId())); ?>"><?php echo($sprint->getName()); ?></a></td>
		<td><input type="hidden" name="sprint[id][]" value="<?php echo $sprint->getId(); ?>" /><input type="checkbox" name="sprint[active][]" value="<?php echo $sprint->getId(); ?>" <?php if($sprint->getActive()):?>checked="checked"<?php endif; ?> /></td>
		<td><input type="radio" name="sprint[current][]" value="<?php echo $sprint->getId(); ?>" <?php if($sprint->getCurrent()):?>checked="checked"<?php endif; ?> /></td>
      </tr>
    <?php $i++; endforeach; ?>
    </tbody>