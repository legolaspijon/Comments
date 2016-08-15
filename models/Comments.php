<?php

class Comments
{

    public $file_path = 'uploads/';

    private $captcha;
    private $errors = [];
    public $file = [];
    public $username;
    public $email;
    public $homepage;
    public $text;
    public $addTime;
    public $ip;
    public $parentId;


    private $goodTypes = [
        'jpg', 'gif',
        'png', 'jpeg', 'plain'
    ];

    /**
     * Загрузка свойств в модель
     * */
    public function load($data)
    {
        foreach ($data as $prop => $value) {
            if (property_exists(__CLASS__, $prop)) {
                $value = ($prop == 'text') ? $this->filter_vars($value, '<a><i><code><strong><') : $this->filter_vars($value);
                if ($prop == 'parentId') {
                    $this->setParentId($value);
                } else {
                    $this->$prop = $value;
                }
            }
        }
        $this->ip = $_SERVER['REMOTE_ADDR'];
    }


    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getCommentTree()
    {
        $comments = $this->all();
        $tree = [];
        $i = 0;
        foreach ($comments as $id => &$node) {
            $i++;
            if (!$node['parent_id']) {
                $tree[$id] = &$node;

            } else {
                $comments[$node['parent_id']]['child'][$id] = &$node;
            }
        }

        return $tree;
    }

    private function htmlComments($tree)
    {
        ob_start();
        include '/views/layouts/comment.php';
        $comment_html = ob_get_contents();
        ob_end_clean();

        return $comment_html;
    }
    

    public function commentsString($data)
    {
        $string = '';
        foreach ($data as $tree) {
            $string .= $this->htmlComments($tree);
        }

        return $string;
    }

    public function all()
    {
        $db = Db::getInstance();
        $pdo = $db->getConnection();
        $stmt = $pdo->query('SELECT * FROM comments');

        $comments = [];
        while ($row = $stmt->fetch()) {
            $comments[$row['id']] = $row;
        }

        return $comments;
    }

    /**
     * Добавление в базу комментария
     * */
    public function addComment()
    {

        $db = Db::getInstance();
        $pdo = $db->getConnection();

        $sql = "INSERT INTO comments (username, email, homepage, text, parent_id, time, ip) VALUES (:username, :email, :homepage, :text, :parent_id, NOW(), '{$this->ip}')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':homepage', $this->homepage);
        $stmt->bindValue(':text', $this->text);
        $stmt->bindValue(':parent_id', $this->parentId);
        $res = $stmt->execute();

        $this->saveImage();

        return $res ? $pdo->lastInsertId() : false;
    }

    public function getById($id)
    {
        $id = intval($id);
        $db = Db::getInstance();
        $pdo = $db->getConnection();
        $sql = "SELECT * FROM comments WHERE id=?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id]);
        $comment = $stmt->fetch();

        $this->username = $comment['username'];
        $this->email = $comment['email'];
        $this->homepage = $comment['homepage'];
        $this->text = $comment['text'];
        $this->ip = $comment['ip'];
        $this->time = $comment['time'];
        $this->filename = $comment['filename'];


        return $this;
    }


    /**
     * Добавление ошибки
     * */
    public function addError($error)
    {
        $this->errors[] = $error;
    }


    public function checkCaptcha()
    {
        $code = $_SESSION['capcha'];
        //echo json_encode(['code1' => $code, 'code2' => $this->captcha]);
        if (strtoupper($this->captcha) != $code) {
            $this->addError('Неверный код капчи');
        }
        if (empty($this->errors)) {
            unset($_SESSION['capcha']);
        }
    }

    public function getCaptcha()
    {
        return $this->captcha;
    }


    public function getPlain(){
        $ip = str_replace('.', '', $this->ip) . "/";
        $text = file_get_contents($this->file_path. $ip .$this->filename);
        return nl2br($text);
    }

    /**
     * Валидация полей
     * */
    public function checkName()
    {
        $len = mb_strlen($this->username, 'utf-8');
        if (!is_string($this->username) || ($len < 2 || $len > 30)) {
            $this->addError('Имя введено не верно оно должно содержать более 2х символов и менее 30');
        }
    }

    public function filter_vars($var, $tagsAccept = null)
    {
        return strip_tags($var, $tagsAccept);
    }

    public function checkEmail()
    {
        $pattern = '#^[\w.]+@\w+\.\w{1,5}$#ui';
        if (!preg_match($pattern, $this->email)) {
            $this->addError('Email введен не верно');
        }
    }

    public function checkHomepage()
    {
        if (!empty($this->homepage)) {
            if (!filter_var($this->homepage, FILTER_VALIDATE_URL)) {
                $this->addError('Не верный url');
            }
        }
    }

    protected function saveImage()
    {
        if (!empty($this->errors)) {
            return;
        }
        $ip = str_replace('.', '', $_SERVER['REMOTE_ADDR']) . "/";
        if(!file_exists($this->file_path . $ip)){
            mkdir($this->file_path . $ip, 0777);
        }
        if (!move_uploaded_file($this->file['tmp_name'], $this->file_path . $ip . $this->file['name'])) {
            $this->addError('Ошибка перемещения файла');
        }
    }

    public function checkImage()
    {
        if (filesize($this->file['tmp_name']) == 0) {
            return false;
        }

        $type = implode(')|(', $this->goodTypes);
        preg_match("/($type)/", $this->file['type'], $gotType);

        if (!empty($gotType[0])) {
            if (($gotType[0] == 'plain') && ($this->file['size'] > 102400)) {
                $this->addError('Слишком большой текстовый файл. Файл должен быть меньше 100Кб.');
            }
        } else {
            $this->addError('Не верный формат файла');
        }

    }

    private function setParentId($parentId)
    {
        preg_match('/(\d+)$/', $parentId, $match);
        $this->parentId = $match[0];
    }

    public function checkText()
    {
        $text = $this->text;
        if (empty($text)) {
            $this->addError('Заполните поле камментария');
            return false;
        }


        preg_match_all('#<([a-z]+)(?: .*)?(?<![/|/ ])>#iU', $text, $result);
        $openedtags = $result[1];
        preg_match_all('#</([a-z]+)>#iU', $text, $result);
        $closedtags = $result[1];
        $lenOpened = count($openedtags);
        //if (count($closedtags) == $lenOpened)
        //$openedtags = array_reverse($openedtags);

        $countTags = 0;
        $tagsIntersection = 0;


        for ($i = 0; $i < $lenOpened; $i++) {
            if (!in_array($openedtags[$i], $closedtags)) {
                $countTags++;
            }
            if ($openedtags[$i] != $closedtags[$i]) {
                $tagsIntersection++;
            }
        }

        if ($countTags) {
            $this->addError('Не правильно расставлены теги в поле "Комментарий"');
            return false;
        }


        $this->text = $text;

    }

    public function validate()
    {
        $this->checkEmail();
        $this->checkHomepage();
        $this->checkName();
        $this->checkText();
        $this->checkImage();
        $this->checkCaptcha();

        if (empty($this->errors)) {
            return true;
        }

        $this->getErrors();

        return false;
    }

    /**
     * Получение ошибок
     * */
    public function getErrors()
    {
        if (!empty($this->errors)) {
            return $this->errors;
        }

        return false;
    }

}
