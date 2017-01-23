<?

class ArticleBimbo extends Article {
	const MYSQL_TABLE = 'article_unknown';
}
class ArticleBimboCollection extends ArticleCollection {
	public function __construct() {
		parent::__construct(new ArticleBimbo());
	}
}

class CommentBimbo extends Comment {
	const MYSQL_TABLE = 'comment_unknown';
}
class CommentBimboCollection extends CommentCollection {
	public function __construct() {
		parent::__construct(new CommentBimbo());
	}
}

class CommentRateBimbo extends CommentRate {
	const MYSQL_TABLE = 'comment_rate_unknown';
}
class CommentRateBimboCollection extends CommentRateCollection {
	public function __construct() {
		parent::__construct(new CommentRateBimbo());
	}
}

class CommentKarmaBimbo extends CommentKarma {
	const MYSQL_TABLE = 'comment_karma_unknown';
}
class CommentKarmaBimboCollection extends CommentKarmaCollection {
	public function __construct() {
		parent::__construct(new CommentKarmaBimbo());
	}
}

class CommentSubscribeBimbo extends CommentSubscribe {
	const MYSQL_TABLE = 'comment_subscribe_unknown';
}
class CommentSubscribeBimboCollection extends CommentSubscribeCollection {
	public function __construct() {
		parent::__construct(new CommentSubscribeBimbo());
	}
}

class VoteBimbo extends Vote {
	const MYSQL_TABLE = 'vote_unknown';
}
class VoteBimboCollection extends VoteCollection {
	public function __construct() {
		parent::__construct(new VoteBimbo());
	}
}

