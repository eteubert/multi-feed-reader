<?php
namespace MultiFeedReader;

const DEFAULT_TEMPLATE = 'default';
const DEFAULT_LIMIT = 15;
const DEFAULT_CACHETIME = 300;
const SHORTCODE = 'multi-feed-reader';

namespace MultiFeedReader\Settings;

const DEFAULT_BEFORE_TEMPLATE = '<table>
<thead>
<th style="width:74px"></th>
<th>Titel</th>
<th>Dauer</th>
</thead>
<tbody>';
const DEFAULT_BODY_TEMPLATE = '<tr class="podcast_archive_element">
	<td class="thumbnail">%THUMBNAIL|64x64%</td>
	<td class="title" style="vertical-align:top">
		<a href="%LINK%"><strong>%TITLE%</strong></a><br/><em>%SUBTITLE%</em>
	</td>
	<td class="duration" style="vertical-align:top">
		%DURATION%
	</td>
</tr>';
const DEFAULT_AFTER_TEMPLATE = '</tbody>
</table>';