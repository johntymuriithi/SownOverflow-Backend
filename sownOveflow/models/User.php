<?php

namespace app\models;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Yii;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use yii\base\NotSupportedException;

class User extends ActiveRecord implements IdentityInterface
{
    public $activationToken;
    public $modelClass = 'app\models\User';
    public $password; // For storing the plaintext password during signup


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'user';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username', 'email', 'level', 'password'], 'required'],
            [['email'], 'email'],
            [['email'], 'unique'],
            [['auth_key'], 'string', 'max' => 32],
            [['password_hash'], 'string', 'min' => 6],
            [['activation_token'], 'string', 'max' => 255],
        ];
    }

    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Finds an identity by the given ID.
     * @param string|int $id the ID to be looked for
     * @return IdentityInterface|null the identity object that matches the given ID.
     */
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    /**
     * Finds an identity by the given token.
     * @param string $token the token to be looked for
     * @param mixed $type the type of the token. The value of this parameter depends on the implementation.
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        $key = Yii::$app->params['jwtSecretKey']; // Ensure this key matches the one used to sign the token
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256')); // Decode the token
            return static::findOne($decoded->data->userId); // Return the user identified by the token
        } catch (\Exception $e) {
            Yii::error('Invalid token: ' . $e->getMessage());
            return null;
        }
    }
    /**
     * Returns an ID that can uniquely identify a user identity.
     * @return string|int an ID that uniquely identifies a user identity.
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * Returns a key that can be used to check the validity of a given identity ID.
     * The key should be unique for each individual user, and should be persistent
     * so that it can be used to check the validity of the user identity.
     * @return string a key that is used to check the validity of a given identity ID.
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * Validates the given authentication key.
     * @param string $authKey the given authentication key
     * @return bool whether the given authentication key is valid.
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    // JSON WEB TOKENS STAFF FROM HERE,,GENERATING JWT FIRST THEN WE VALIDATE

    public function generateJwt()
    {
        $key = Yii::$app->params['jwtSecretKey'];
        $payload = [
            'iat' => time(),
            'nbf' => time(),
            'exp' => time() + 580675565,
            'data' => [
                'userId' => $this->id,
                'username' => $this->username,
                'level' => $this->level,
                'email' => $this->email,// Add roles to the payload
            ],
        ];
        return JWT::encode($payload, $key, 'HS256');
    }

    public static function validateJwt($token)
    {
        $key = Yii::$app->params['jwtSecretKey'];
        try {
            $decoded = JWT::decode($token, new Key($key, 'HS256'));
            Yii::info("Decoded token: " . json_encode($decoded), __METHOD__);
            return $decoded;
        } catch (\Exception $e) {
            Yii::error("JWT validation failed: " . $e->getMessage(), __METHOD__);
            return null;
        }
    }

    public function generateActivationToken()
    {
        $this->activationToken = Yii::$app->security->generateRandomString() . '_' . time();
    }
}

?>