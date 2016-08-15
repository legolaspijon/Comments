<?php

class SiteController
{
    public function indexAction()
    {
        $model = new Comments();
        $commentsTree = $model->getCommentTree();
        $commentsHtml = $model->commentsString($commentsTree);

        require_once ROOT . "/views/site/index.php";

    }

/*    public function fileAction($id){
        $model = new Comments();
        $comment = $model->getById($id);

        if(preg_match('/.txt$/iu', $comment->filename)){
            $text = $model->getPlain();
        } else {
            $image = $model->getImg();
        }
        
        require_once '/views/site/file.php';
    }*/

    public function ajaxAction()
    {
        $model = new Comments();
        $model->load($_POST);
        
        if(isset($_FILES) && !empty($_FILES)){
            $model->setFile($_FILES['file']);
        }

        if ($model->validate()) {
            $model->addComment();

            $comment_html = <<<HERE
            <li class='new_comment'>
                <div class="comments-item">
                    <div class="comment-title">
                        <h5>
                            <a href="{$model->filter_vars($_POST['homepage'])}">{$model->filter_vars($_POST['username'])}</a>
                            <sapn class="comment-time">{$_POST['time']}</sapn>
                        </h5>
                    </div>
                    <div class="comment-body">{$model->filter_vars($_POST['text'], '<i><a><code><strong>')}</div>
                    <div class="comment-footer">
                        {$model->filter_vars($_POST['email'])}
                    </div>
                </div>
            </li>

HERE;
            echo json_encode(['comment' => $comment_html]);
        } else {
            $errors = $model->getErrors();
            $error_html = "<div class='alert alert-danger error_place'><ul>";
            foreach ($errors as $error) {
                $error_html .= '<li>' . $error . '</li>';
            }
            $error_html .= "</ul></div>";

            echo json_encode(['errors' => $error_html]);
        }

        return false;
    }
}