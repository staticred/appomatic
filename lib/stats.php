<?php

class srStats {
  
  public $items;
  public $trend_gravity = "1.8";
  public $timecode;
  public $trend;
  
  
  /**
    * calculates a list of trending items
    *
    * To use, assign an array of items $this->items in the originating code.
    * The function will calculate the trend and return a sorted array to 
    * $this->trend.
    *
    * Usage example:
    *
    * $post_stats = new srStats();
    * 
    * foreach ($posts as $post) {
    *   $post_stats->items['id'] = $post['id'];  
    *   $post_stats->items['date'] = $post['date'];
    * }
    * 
    * $post_stats->build_trend;
    *
    * forech($post_stats->trend as $id => $score) {
    *   print($posts[$id]['name'] " . " : $score");
    * }
    *
    *
    * @param int $this->items['id'] 
    *   The ID of the post
    * @param int $this->items['date']
    *   The date of the post as a UNIX time stamp
    * @return array
    *   Returns an array of IDs sorted by score
    */
    
  public function build_trend() {
  
    $this->trend = array();
  
    foreach ($this->items as $item) {
      $score = $this->calc_score($item['score'], $item['date'], $this->gravity);
      $this->trend[$item['id']] = $score;
    }
    
    natsort($this->trend);
  
  }
  
  /**
    * Calculates the age of an item.
    */
  private function calc_age($item_date) {
    // grab current time.
    $now = time();
    
    // Just in case we get handed a string accidentally.
    if (!is_numeric($item_date)) {
      $item_date = strtotime($item_date);
    }
    
    // return the difference in hours between now and then.
    return ($now - $item_date) / 60;
  }

    /** 
      * Originally from http://trendn.tumblr.com/post/5142370730/how-the-trendn-ranking-algorithm-works: 
      * 
      *   The ranking algorithm looks like this
      *   
      *   Score = P / (T+2)^G
      *   
      *   Where,
      *   P = points of an item (I will discuss this number next)
      *   T = time since submission (in hours)
      *   G = Gravity, defaults to 1.8
      *   
      *   Now to explain..
      *   
      *   P = Points of an item: This essentially is the sum of all the social engagement factors. For example, we use the Facebook API to get the amount of likes, shares and comments. Many other factors are used:
      *   Twitter - Tweet Count
      *   Digg - Digg Count, Comment Count
      *   Reddit - Votes, Comment Count
      *   Bookmarks - Delicious, Google Reader
      *   Hacker News - Points, Comments
      *   
      *   T = Time since submission: The score decreases as T increases, meaning that older items will get lower and lower scores.
      *   
      *   G = Gravity: The score decreases much faster for older items if gravity is increased      
      *
      * @return int score of item
    */  
  private function calc_score($score, $date, $gravity = 1.8) {
  
    $age = $this->calc_age($date);
    
    return ($score - 1) / pow(($age+2), $gravity);
    
  }

}
