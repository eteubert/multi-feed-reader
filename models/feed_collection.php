<?php
namespace MultiFeedReader\Models;

class FeedCollection extends Base
{

}

FeedCollection::property( 'id', 'INT NOT NULL AUTO_INCREMENT PRIMARY KEY' );
FeedCollection::property( 'name', 'VARCHAR(255)' );
FeedCollection::property( 'before_template', 'TEXT' );
FeedCollection::property( 'body_template', 'TEXT' );
FeedCollection::property( 'after_template', 'TEXT' );
// FeedCollection::has_many( 'MultiFeedReader\Models\Feed' );
FeedCollection::build();