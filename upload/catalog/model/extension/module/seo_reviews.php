<?php
class ModelExtensionModuleSeoReviews extends Model {
    public function getReviewPage($product_id) {
		$this->load->language('product/product');

		$this->load->model('catalog/review');

		if (isset($this->request->get['page'])) {
			$page = (int)$this->request->get['page'];
		} else {
			$page = 1;
		}

		$data['reviews'] = array();

		$review_total = $this->model_catalog_review->getTotalReviewsByProductId($product_id);

        $reviews_count =  ($this->config->get('seo_reviews_count')) ? $this->config->get('seo_reviews_count') : 5;
		$results = $this->model_catalog_review->getReviewsByProductId($product_id, ($page - 1) * $reviews_count, $reviews_count);

		foreach ($results as $result) {
			$data['reviews'][] = array(
				'author'     => $result['author'],
				'text'       => nl2br($result['text']),
				'rating'     => (int)$result['rating'],
				'date_added' => date($this->language->get('date_format_short'), strtotime($result['date_added']))
			);
		}

		$pagination = new Pagination();
		$pagination->total = $review_total;
		$pagination->page = $page;
		$pagination->limit = $reviews_count;
		$pagination->url = $this->url->link('product/product/review', 'product_id=' . $product_id . '&page={page}');

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($review_total) ? (($page - 1) * $reviews_count) + 1 : 0, ((($page - 1) * $reviews_count) > ($review_total - $reviews_count)) ? $review_total : ((($page - 1) * $reviews_count) + $reviews_count), $review_total, ceil($review_total / $reviews_count));

		return $reviews_html = $this->load->view('product/review', $data);
	}
}