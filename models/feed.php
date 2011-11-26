<?php
namespace MultiFeedReader\Models;

class Feed extends Base
{
}

Feed::property( 'feed_collection_id', 'INT' );
Feed::property( 'url', 'VARCHAR(255)' );
// Feed::belongs_to( 'MultiFeedReader\Models\FeedCollection' );
Feed::build();