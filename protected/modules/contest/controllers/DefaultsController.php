<?php
class DefaultController extends YBackController
{
    /**
     * Displays a particular model.
     * @param integer $id the ID of the model to be displayed
     */
    public function actionView($id)
    {
        $this->render('view', array(
                                   'model' => $this->loadModel($id),
                              ));
    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model = new Contest;

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Contest']))
        {
            $model->attributes = $_POST['Contest'];

            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('create', array(
                                     'model' => $model,
                                ));
    }

    /**
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model = $this->loadModel($id);

        // Uncomment the following line if AJAX validation is needed
        // $this->performAjaxValidation($model);

        if (isset($_POST['Contest']))
        {
            $model->attributes = $_POST['Contest'];
            if ($model->save())
                $this->redirect(array('view', 'id' => $model->id));
        }

        $this->render('update', array(
                                     'model' => $model,
                                ));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id)
    {
        if (Yii::app()->request->isPostRequest)
        {
            // we only allow deletion via POST request
            $this->loadModel($id)->delete();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl']
                                    : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }

    /**
     * Lists all models.
     */
    public function actionIndex()
    {
        $dataProvider = new CActiveDataProvider('Contest');

        $this->render('index', array(
                                    'dataProvider' => $dataProvider,
                               ));
    }

    /**
     * Manages all models.
     */
    public function actionAdmin()
    {
        $model = new Contest('search');
        $model->unsetAttributes(); // clear any default values
        if (isset($_GET['Contest']))
            $model->attributes = $_GET['Contest'];

        $this->render('admin', array(
                                    'model' => $model,
                               ));
    }

    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer the ID of the model to be loaded
     */
    public function loadModel($id)
    {
        $model = Contest::model()->findByPk((int)$id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    /**
     * Performs the AJAX validation.
     * @param CModel the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'contest-form')
        {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

    public function actionDeleteImage($id)
    {
        if (Yii::app()->request->isPostRequest)
        {
            ImageToContest::model()->findByPk((int)$id)->delete();

            if (!isset($_GET['ajax']))
            {
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl']
                                    : array('admin'));
            }

        }
        else        
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');        
    }

    public function actionAddImage($contest_id)
    {
        $contest = $this->loadModel((int)$contest_id);

        $image = new Image;

        if (Yii::app()->request->isPostRequest)
        {
            $transaction = Yii::app()->db->beginTransaction();

            try
            {                
                if ($image->create($_POST['Image']))
                {
                    if ($contest->addImage($image))                    
                        Yii::app()->user->setFlash(YFlashMessages::NOTICE_MESSAGE, Yii::t('contest', 'Фотография добавлена!'));                    

                    $transaction->commit();

                    $this->redirect(array('/contest/default/view/', 'id' => $contest->id));
                }

                throw new CDbException(Yii::t('contest', 'При добавлении изображения произошла ошибка!'));
            }
            catch (Exception $e)
            {
                $transaction->rollback();

                Yii::app()->user->setFlash(YFlashMessages::ERROR_MESSAGE, Yii::t('contest','Произошла ошибка!'));
            }

            $this->redirect(array('/contest/default/addImage/', 'id' => $contest->id));
        }

        $this->render('addImage', array('contest' => $contest, 'model' => $image));
    }
}