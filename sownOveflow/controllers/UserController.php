<?php
namespace app\controllers;

use Cassandra\Value;
use PHPUnit\Framework\Constraint\Count;
use Yii;
use yii\base\Security;
use yii\filters\AccessControl;
use yii\rest\ActiveController;
use app\models\User;
use yii\web\ConflictHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\BadRequestHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\UnauthorizedHttpException;
//use app\models\PasswordResetToken;

class UserController extends BaseController
{
    public $modelClass = 'app\models\User';// specifies the model this controller will use


    public function actionSignup()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = new User();
        $user->username = Yii::$app->request->post('username');
        $user->level = Yii::$app->request->post('level');
        $user->email = Yii::$app->request->post('email');
        $user->password = Yii::$app->request->post('password');
        $requiredFields = ['username', 'level', 'email', 'password'];

        foreach ($requiredFields as $field) {
            if (!isset($params[$field])) {
                return ['status' => false, 'message' => "Field '$field' is required"];
            }
        }
        if (User::findOne(['email' => $params['email']])) {
            throw new ConflictHttpException("User with the same Email Exists");
        }


        // to here

        $user->username = $params['username'];
        $user->level = $params['level'];
        $user->email = $params['email'];
        $user->setPassword($params['password']);
        $user->generateAuthKey();
        $user->generateActivationToken();

        if ($user->save()) {
            $auth = Yii::$app->authManager;
//            Yii::$app->mailer->compose()
//                ->setFrom('ecleStay-no-reply@gmail.com')
//                ->setTo($user->email)
//                ->setSubject('Welcome to ecleStay')
//                ->setHtmlBody("<p>Welcome {$user->first_name} {$user->second_name}, to <h1>EcliStay</h1></p>
//<p>Please click below Button to activate your account</p>
//<a href='https://d6a6-41-80-114-128.ngrok-free.app/user/activateuser?token={$user->activationToken}'>Activate Account</a>")
//                ->send();
            return ['status' => 200, 'message' => 'User Sign up was Successful'];
        } else {
            return ['status' => false, 'errors' => $user->errors];
        }
    }

    // helper for signup, doing some staffy here
//    public static function getSome($email) {
//        $user = User::findOne(['email' => $email]);
//        return $user['id'];
//    }

    public function actionLogin()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        $params = Yii::$app->request->bodyParams;
        $user = User::findOne(['email' => $params['email']]);
        if (!$user) {
            throw new NotFoundHttpException("User does not exists");
        } else {
            if ($user && Yii::$app->security->validatePassword($params['password'], $user->password_hash)) {
                $tokenJWTs = $user->generateJwt();
                        // give the user role her
                return ['data' => [ 'username' => $user->username, 'email' => $user->email, 'id' => $user->id, 'token' => $tokenJWTs]];
            } else {
                throw new BadRequestHttpException('Invalid password');
            }
        }
    }

//    public function actionResetpasswordlink() {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $args = Yii::$app->request->bodyParams;
//        $user = User::findOne(['email' => $args['email']]);
//
//        if ($user) {
//            $userId = $user->id;
//            $token = Yii::$app->security->generateRandomString(64);
//            $expireDate = time() + 12000000;
//            if (PasswordResetToken::createToken($userId, $token, $expireDate)) {
//                // Send the token to the user via email
//                $resetLink = Yii::$app->urlManager->createAbsoluteUrl(['reset', 'token' => $token]);
//                Yii::$app->mailer->compose()
//                    ->setFrom('ecleStay-password-reset@gmail.com')
//                    ->setTo($user->email)
//                    ->setSubject('Password Reset')
//                    ->setHtmlBody("<p>Hello {$user->first_name},</p><p>We have recieved a reqeust for a reset of password.
//Click link below change your password <h1>Reset Link:</h1><i><a href='{$resetLink}'>Reset Password Here</a></i>
//<p>Please do ignore this Link if you didn't request it, Thank You</p>")
//                    ->send();
//
//                return ["Status" => '200 OK', "resentLink" => $resetLink, "token" => $token];
//            } else {
//                throw new \RuntimeException('Failed to create password reset token.');
//            }
//        } else {
//            throw new UnauthorizedHttpException("User with the email does not exists");
//        }
//    }

//    public function actionResetpassword($token)
//    {
//        Yii::$app->response->format = Response::FORMAT_JSON;
//        $userParams = Yii::$app->request->bodyParams;
//        $hashedTokenFromUser = hash('sha256', $token);
//        $user = User::findOne(['email' => $userParams['email']]);
//        if (!$user) {
//            throw new NotFoundHttpException("Email user does not exist");
//        }
//        $userId = $user->id;
//        $userRows = PasswordResetToken::findAll(['user_id' => $userId]);
//        $validToken = null;
//        foreach ($userRows as $idToken) {
//            if ($idToken->token === $hashedTokenFromUser && $idToken->token_expiry > time()) {
//                $validToken = $idToken;
//                break;
//            }
//        }
//        if ($validToken) {
//            $newPassword = $userParams['password'];
//            $newPasswordHashed = Yii::$app->security->generatePasswordHash($newPassword);
//            $user->password_hash = $newPasswordHashed;
//
//            if ($user->blocked === true) {
//                $user->blocked = false;
//                $user->login_trials = 0;
//                $user->save();
//            }
//            if ($user->save()) {
//                foreach ($userRows as $row) {
//                    $row->delete();
//                }
//                return ["status" => 200, "message" => "Reset Successful, Continue with $newPassword as your password"];
//            } else {
//                throw new NotAcceptableHttpException("Not acceptable, Sorry");
//            }
//        } else {
//            throw new NotFoundHttpException("Invalid or expired token");
//        }
//    }

    // get the number of registered users in our website

    public function actionUserstotal() {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $users = User::find()->all();
        $total = [];
        if ($users) {
            foreach ($users as $user) {
                $reviewData = [
                    'host_id' => $user->id,
                    'user_name' => $user->first_name . " " . $user->second_name,
                    "email" => $user->email,
                    'roles' => $this->helper($user->id)
                ];

                $total[] = $reviewData;
            }
        } else {
            throw new NotFoundHttpException("No Users  Found, Try again later");
        }

        return ["status" => 200, "message" => "Users retrived succcesifully", "totalUsers" => count($total)];
    }

    public function helper ($id) {
        // Get the authManager component
        $roles = Yii::$app->authManager->getRolesByUser($id);
        return array_keys($roles);
    }

}
?>