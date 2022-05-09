<?php
//! require once
require_once(dirname(__FILE__) . '/../utils/database.php');


//! CLASS POST
class Post
{
    private int $id;
    private string $post_at;
    private string $post;
    private string $id_user;


    /** //! construct
     * @param int $id
     * @param string $post_at
     * @param string $post
     * @param string $id_user
     */
    function __construct(
        string $post_at = '',
        string $post = '',
        string $id_user = ''
    ) {
        $this->setPost_at($post_at);
        $this->setPost($post);
        $this->setId_user($id_user);
        //! pdo
        $this->pdo = Database::dbConnect();
    }


    /** //! getId()
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }
    /** //! setId(int $id)
     * @param int $id
     * 
     * @return void
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }


    /** //! getPost_at()
     * @return string
     */
    public function getPost_at(): string
    {
        return $this->post_at;
    }
    /** //! setPost_at(string $post_at)
     * @param string $post_at
     * 
     * @return void
     */
    public function setPost_at(string $post_at): void
    {
        $this->post_at = $post_at;
    }


    /** //! getPost()
     * @return string
     */
    public function getPost(): string
    {
        return $this->post;
    }
    /** //! setPost(string $post)
     * @param string $post
     * 
     * @return void
     */
    public function setPost(string $post): void
    {
        $this->post = $post;
    }


    /** //! getId_user()
     * @return string
     */
    public function getId_user(): string
    {
        return $this->id_user;
    }
    /** //! setId_user(string $id_user)
     * @param string $id_user
     * 
     * @return void
     */
    public function setId_user(string $id_user): void
    {
        $this->id_user = $id_user;
    }


    /** //! add()
     * @return bool
     */
    public function add(): bool
    {
        try {
            $sql = 'INSERT INTO `posts` (`post_at`, `post`, `id_user`)
                    VALUES (:post_at, :post, :id_user);';

            $sth = $this->pdo->prepare($sql);
            $sth->bindValue(':post_at', $this->getPost_at(), PDO::PARAM_STR);
            $sth->bindValue(':post', $this->getPost(), PDO::PARAM_STR);
            $sth->bindValue(':id_user', $this->getId_user(), PDO::PARAM_STR);

            $sth->execute();

            if (!$sth) {
                throw new PDOException();
            } else {
                return true;
            }
        } catch (PDOException $e) {
            return false;
        }
    }


    /** //! getAll()
     * @return array
     */
    public static function getAll(int $limit = 0, int $offset = OFFSET,  $search = null): array
    {
        try {
            $sql = 'SELECT
                    `posts`.`id` AS `id`,
                    `posts`.`post_at`,
                    `posts`.`post`,
                    `posts`.`id_user` AS `id_user`,
                    `users`.`pseudo`,
                    `avatars`.`avatar`
                    FROM `trotter`.`posts`
                    LEFT JOIN `users` ON `posts`.`id_user` = `users`.`id`
                    LEFT JOIN `avatars` ON `avatars`.`id` = `users`.`id_avatars`';
            if (!is_null($search)) {
                $sql .= ' WHERE `posts`.`post` LIKE :search';
            }
            $sql .= ' ORDER BY `post_at` DESC
                    LIMIT :limit, :offset;';

            $sth = Database::DbConnect()->prepare($sql);
            $sth->bindValue(':limit', $limit, PDO::PARAM_INT);
            $sth->bindValue(':offset', $offset, PDO::PARAM_INT);

            if (!is_null($search)) {
                $sth->bindValue(':search', "%$search%", PDO::PARAM_STR);
            }

            if (!$sth) {
                throw new PDOException();
            } else {
                $sth->execute();
                $posts = $sth->fetchAll();
            }

            return $posts;
        } catch (PDOException $e) {
            return [];
        }
    }


    /** //! getLastByIdUser()
     * @return array
     */
    public static function getLastByIdUser(int $id = 0): array
    {
        try {
            $sql = 'SELECT *
                    FROM `trotter`.`posts`
                    WHERE `id_user` = :id
                    ORDER BY `post_at` DESC
                    LIMIT 0, 5;';

            $sth = Database::DbConnect()->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_INT);

            if (!$sth) {
                throw new PDOException();
            } else {
                $sth->execute();
                $posts = $sth->fetchAll();
            }

            return $posts;
        } catch (PDOException $e) {
            return [];
        }
    }


    /** //! getOneById(int $id)
     * @param int $id
     * 
     * @return object
     */
    public static function getOneById(int $id): object
    {
        try {
            $sql = 'SELECT *
                    FROM `trotter`.`posts`
                    WHERE `posts`.`id` = :id;';

            $sth = Database::DbConnect()->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_INT);
            $sth->execute();

            if (!$sth) {
                throw new PDOException('le message n\'existe pas');
            } else {
                $user = $sth->fetch();
            }

            if (!$user) {
                throw new PDOException('le message n\'existe pas');
            } else {
                return $user;
            }
        } catch (PDOException $e) {
            return $e;
        }
    }


    /** //! delete(int $id)
     * @param int $id
     * 
     * @return bool
     */
    public static function delete(int $id): bool
    {
        try {
            $sql = 'DELETE FROM `trotter`.`posts`
                    WHERE `id` = :id;';

            $sth = Database::DbConnect()->prepare($sql);
            $sth->bindValue(':id', $id, PDO::PARAM_INT);

            if (!$sth) {
                throw new PDOException();
            } else {
                $sth->execute();

                $count = $sth->rowCount();
                return ($count <= 0) ? false : true;
            }
        } catch (PDOException $e) {
            return false;
        }
    }


    /** //! count()
     * @return int
     */
    public static function count(): int
    {
        try {
            $sql = 'SELECT COUNT(*) FROM `trotter`.`posts`;';

            $sth = Database::DbConnect()->prepare($sql);
            $sth->execute();

            if (!$sth) {
                throw new PDOException();
            } else {
                $count = $sth->fetchColumn();
            }
            return $count;
        } catch (PDOException $e) {
            return 0;
        }
    }
}
