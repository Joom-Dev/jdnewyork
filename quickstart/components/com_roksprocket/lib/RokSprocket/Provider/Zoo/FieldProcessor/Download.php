<?php
/**
 * @version   $Id: Download.php 13467 2013-09-13 23:41:54Z btowles $
 * @author    RocketTheme http://www.rockettheme.com
 * @copyright Copyright (C) 2007 - 2020 RocketTheme, LLC
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 only
 */


class RokSprocket_Provider_Zoo_FieldProcessor_Download extends RokSprocket_Provider_Zoo_FieldProcessor_Basic implements RokSprocket_Provider_Zoo_LinkFieldProcessorInterface
{
	/**
	 * @param Element $element
	 *
	 * @return RokSprocket_Item_Link
	 */
	public function getAsSprocketLink(Element $element)
	{
		$image_field_data = $this->getValue($element, true);
		$link             = new RokSprocket_Item_Link();
		$link->setUrl(((isset($image_field_data['file'])) ? $image_field_data['file'] : ''));
		$link->setIdentifier('link_field_' . $element->identifier);
		return $link;
	}

}
