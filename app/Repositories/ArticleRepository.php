<?php

namespace App\Repositories;

use App\Article;
use App\Scopes\DraftScope;

class ArticleRepository
{
    use Repository;

    protected $model;

    protected $visitor;

    public function __construct(Article $article, VisitorRepository $visitor)
    {
        $this->model = $article;

        $this->visitor = $visitor;
    }

    /**
     * Get the page of articles without draft scope.
     *
     * @param  integer $number
     * @param  string  $sort
     * @param  string  $sortColumn
     * @return collection
     */
    public function page($number = 10, $sort = 'desc', $sortColumn = 'created_at')
    {
        $this->model = $this->checkAuthScope();

        return $this->model->orderBy($sortColumn, $sort)->paginate($number);
    }

    /**
     * Get the article record without draft scope.
     *
     * @param  int $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->model->withoutGlobalScope(DraftScope::class)->findOrFail($id);
    }

    /**
     * Update the article record without draft scope.
     *
     * @param  int $id
     * @param  array $input
     * @return boolean
     */
    public function update($id, $input)
    {
        $this->model = $this->model->withoutGlobalScope(DraftScope::class)->findOrFail($id);

        return $this->save($this->model, $input);
    }

    /**
     * Get the article by article's slug.
     * The Admin can preview the article if the article is drafted.
     *
     * @param $slug
     * @return object
     */
    public function getBySlug($slug)
    {
        $this->model = $this->checkAuthScope();

        $article = $this->model->where('slug', $slug)->firstOrFail();

        $article->increment('view_count');

        $this->visitor->log($article->id);

        return $article;
    }

    /**
     * Check the auth and the model without global scope when user is the admin.
     *
     * @return Model
     */
    public function checkAuthScope()
    {
        if (auth()->check() && auth()->user()->is_admin) {
            $this->model = $this->model->withoutGlobalScope(DraftScope::class);
        }

        return $this->model;
    }

    /**
     * Sync the tags for the article.
     *
     * @param  array $tags
     * @return Paginate
     */
    public function syncTag(array $tags)
    {
        $this->model->tags()->sync($tags);
    }

    /**
     * Search the articles by the keyword.
     *
     * @param  string $key
     * @return collection
     */
    public function search($key)
    {
        $key = trim($key);

        return $this->model
                    ->where('title', 'like', "%{$key}%")
                    ->orderBy('published_at', 'desc')
                    ->get();

    }

    /**
     * Delete the draft article.
     *
     * @param int $id
     * @return boolean
     */
    public function destroy($id)
    {
        return $this->getById($id)->delete();
    }

    /**
     * get a list of tag ids associated with the current article
     * @return [array]
     */
    public function getTagListAttribute()
    {
        return $this->tags->pluck('id')->all();
    }

    /**
     * 范围查询
     * @param $query
     * @param $userId
     * @return mixed
     */
    public function scopeUserId($query, $userId)
    {
        return $query->where('user_id', '=', $userId);
    }

    /**
     * get article model
     * @param int $id
     * @return mixed
     */
    public function getArticleModel($id)
    {
        if (is_numeric($id)) {
            return $this->model->findOrFail($id);
        } else {
            return $this->model->where('slug', '=', $id)->first();
        }
    }


    /**
     * get archived articles
     * @param int $year
     * @param int $month
     * @param int $limit
     * @return mixed
     */
    public function getArchivedArticleList($year, $month, $limit = 8)
    {
        return $this->model->select(['id','title','slug','content','created_at','category_id'])
                ->where(DB::raw("DATE_FORMAT(`created_at`, '%Y %c')"), '=', "$year $month")
                ->where('category_id', '<>', 0)
                ->latest()
                ->paginate($limit);
    }

    /**
     * get archive list of articles
     * @param  integer $limit [description]
     * @return [type]         [description]
     */
    public function getArchiveList($limit = 12)
    {
        return $this->model->select(DB::raw("DATE_FORMAT(`created_at`, '%Y %m') as `archive`, count(*) as `count`"))
                ->where('category_id', '<>', 0)
                ->groupBy('archive')
                ->orderBy('archive', 'desc')
                ->limit($limit)
                ->get();
    }

    /**
     * get latest articles
     * @param int $pageNum
     * @return mixed
     */
    public function getLatestArticleList($pageNum = 10)
    {
        return $this->model->select(['id','title','slug','content','created_at','category_id'])
                ->where('category_id', '<>', 0)
                ->orderBy('id', 'desc')
                ->paginate($pageNum);
    }

    /**
     * get articles of the given category
     * @param $categoryId
     * @param int $limit
     * @return mixed
     */
    public function getArticleListByCategoryId($categoryId, $limit = 10)
    {
        return $this->model->select(['id','title','slug','content','created_at','category_id'])
                ->where('category_id', $categoryId)
                ->orderBy('id', 'desc')
                ->paginate($limit);
    }

    /**
     * get hot articles
     * @param int $limit
     * @return mixed
     */
    public function getHotArticleList($limit = 3)
    {
        $select = [
            'articles.id',
            'articles.pic',
            'articles.title',
            'articles.slug',
            'articles.created_at',
            'article_status.views',
        ];
        return $this->model->select($select)
                ->leftJoin('article_status', 'articles.id', '=', 'article_status.article_id')
                ->where('category_id', '<>', 0)
                ->orderBy('article_status.views', 'desc')
                ->limit($limit)
                ->get();
    }

    /**
     * get articles associated with the given keyword
     * @param $keyword
     * @return mixed
     */
    public function getArticleListByKeyword($keyword)
    {
        return $this->model->select(['id','title','slug','content','created_at','category_id'])
                ->where('title', 'like', "%$keyword%")
                ->orWhere('content', 'like', "%$keyword%")
                ->where('category_id', '<>', 0)
                ->orderBy('id', 'desc')
                ->paginate(8);
    }

}