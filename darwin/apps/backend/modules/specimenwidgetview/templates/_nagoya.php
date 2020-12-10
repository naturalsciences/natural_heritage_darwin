<table class="catalogue_table_view">
  <tbody>
    <tr>
		<td>
			<?php if ($sf_user->isAtLeast(Users::ENCODER)) : ?>
				<?php if ( $spec->getNagoya() == 'yes'):?>
						Concerned by the Nagoya protocol
				<?php else:?>
					<?php if ( $spec->getNagoya() == 'no'):?>
						Not concerned by the Nagoya protocol
					<?php	else:?>
						Nagoya protocol not defined
					<?php endif ; ?>
				<?php endif ; ?>
			<?php else:?>
				Not allowed
			<?php endif ; ?>
		</td>
    </tr>
  </tbody>
</table>