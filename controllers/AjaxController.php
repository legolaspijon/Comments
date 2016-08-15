
<?php

    class AjaxController{

        public function addAction(){
            $model = new Comments();

            $model->load($_POST);

            if($model->validate()){

                $model->addComment();

                $comment_html = <<<HERE
            <li class='new_comment'>
                <div class="comments-item">
                    <div class="comment-title">
                        <h5>
                            <a href="{$_POST['homepage']}">{$_POST['username']}</a>
                            <sapn class="comment-time">{$_POST['time']}</sapn>
                        </h5>
                    </div>
                    <div class="comment-body">{$_POST['text']}</div>
                    <div class="comment-footer">
                        {$_POST['email']}
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