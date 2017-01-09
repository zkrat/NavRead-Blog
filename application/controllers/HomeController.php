


<?php

//session_start(); 

defined('BASEPATH') OR exit('No direct script access allowed');



class HomeController extends CI_Controller {


   function __construct(){
          parent::__construct();
          $this->load->helper("security");

         $this->load->library('session');
         $this->load->model('Home_model');
         //$this->load->helper('uri');


/*header("Cache-Control: no-store, must-revalidate, max-age=3");
header("Pragma: no-cache");*/
//header("Expires: Sat, 26 Jul 1997 05:00:00 GMT");



   }

	public function index()
	{  
		  
        
			$this->load->view('UserRegister');
    

	}
     
	 public function signupAction()
	{

    if($this->Home_model->_check_module_task_auth())
    {
      $this->load->view('UserHomeView');
     }

     else
     {
  
		$this->load->library('form_validation');

		 $config = array(
        array(
                'field' => 'name',
                'label' => 'Name',
                'rules' => 'required|xss_clean'
        ),
        
        array(
                'field' => 'passwd',
                'label' => 'Password',
                'rules' => 'required|xss_clean',
                'errors' => array(
                        'required' => 'You must provide a %s.',
                ),
        ),
        array(
                'field' => 'Rpasswd',
                'label' => 'Password Confirmation',
                'rules' => 'required|matches[passwd]|xss_clean'
        ),
        array(
                'field' => 'emailid',
                'label' => 'Email',
                'rules' => 'valid_email|callback_email_check|xss_clean'
        )
);

$this->form_validation->set_rules($config);


		if($this->form_validation->run()==FALSE)
		{     
			redirect('HomeController/userregView');


		}
		else
		{
            

			$data['name'] = $this->input->post('name');
			$data['emailid'] = $this->input->post('emailid');
			$data['passwd'] = $this->input->post('passwd');

			

			if($this->Home_model->setData($data))
            {
                 echo "You have been signed up. You may now log in.";
                 //redirect(base_url('/UserRegister'));
                 redirect('HomeController/userregView');
            }
            else
            {
              echo "Signup Failure.";
              //redirect(base_url('/UserRegister'));
              redirect('HomeController/userregView');
            }



     }			
        
		}

	}



 public function email_check()
 {  

  if($this->Home_model->_check_module_task_auth())
  {
     $this->load->view('UserHomeView');
   }
else
{
 	

  // $this->Home_model->email_check_model($email);
 	
 	//$this->form_validation->set_message('email_check', $email);
 	$email = $this->input->post('emailid');
 	//return false;
 	if($this->Home_model->email_check_model($email))
 		 return true;
    else
        {
        	$this->form_validation->set_message('email_check', 'The email '.$email.' is already registerted.');
        	return false;

        } 
         	
          }


 }

  public function loginAction()
	{
        //echo 'Hiiiiiiiii';
    /*  if(isset($_SESSION['email']))
      {

           
        $this->load->view('UserHomeView');
       }     
     else
     {  
      */
        $this->load->library('form_validation');

        $config = array(

         array(
                'field' => 'emailid',
                'label' => 'Email',
                'rules' => 'required|valid_email|xss_clean'
        ),
       
        array(
                'field' => 'passwd',
                'label' => 'Password',
                'rules' => 'required|callback_validateUser|xss_clean',
                'errors' => array(
                        'required' => 'You must provide a %s.',
             )
       
         )
        
        
);


      $this->form_validation->set_rules($config);


		if($this->form_validation->run()==FALSE)
		{     
			 if($this->Home_model->_check_module_task_auth())
      {

           
        $this->load->view('UserHomeView');
       }     
      else
      {
       //$this->load->view('UserRegister');

        header('location: http://127.0.0.1/NavRead/index.php/HomeController/');
        die;
 
      } 
      


		}
		else
		{
            
  			 $email = $this->input->post('emailid') ; 
                    
			 	    //$password =  $this->input->post('passwd') ;

	
              $this->session->set_userdata('email',$email);

               
              //$_SESSION['password'] = $password;


            $this->load->view('UserHomeView',$this->session->userdata);



			

		}
     

  // }

  

	}


  

	public function validateUser()
        {
if($this->Home_model->_check_module_task_auth())
{ 
  
  $this->load->view('UserHomeView');

 }

 else
 {

        
          	$email = $this->input->post('emailid');
          	$password = $this->input->post('passwd');

          	if($this->Home_model->validateUser_model($email,$password))
 		           {

 		           	return true;

 		           }
    else
        {
        	$this->form_validation->set_message('validateUser' , 'Wrong Email-password combination. PLease try again.');
        	return false;

        } 
          
}


  }



  public function blogAction()
  {
  	  
      if($this->Home_model->_check_module_task_auth())
      {

     $this->load->library('form_validation');

        $config = array(

         array(
                'field' => 'blogname',
                'label' => 'blog-name',
                'rules' => 'required|callback_blogname_check|xss_clean'
        ),
       
        array(
                'field' => 'blogcontent',
                'label' => 'blog-content',
                'rules' => 'required|xss_clean',
                'errors' => array(
                        'required' => 'You must write some %s.',
             )
       
         )
        
);


      $this->form_validation->set_rules($config);

      if($this->form_validation->run()==FALSE)
		{     
			$this->load->view('UserHomeView');


		}
		else
		{
            
  			     $data['email'] =$this->session->userdata('email') ; 
                   
             $data['blogname'] = $this->input->post('blogname');

             $data['blogcontent'] = $this->input->post('blogcontent');     


           

			if($this->Home_model->setBlogData($data))
            {
                 echo 'Your blog '.$data['blogname'].' has been published!';

                 $res = $this->Home_model->sendMailtoSubscribers($data);

                //echo print_r($res);

                  if($res)
                  {
                    echo 'mail Sent';
                  }
                  else{
                    //echo 'mail sending failed';
                  }

                 $this->load->view('UserHomeView');
            }
            else
            {
              echo "Oops! Your blog could not be published at this moment.";
              $this->load->view('UserHomeView');
            }
			

		}

  }

  else
      {
        //redirect(base_url('/UserRegister'));
        redirect('HomeController/userregView');
      }
  
  }


  public function blogname_check()
  {
 
   if($this->Home_model->_check_module_task_auth())
   {

     
 	$blogname = $this->input->post('blogname');
 	
 	if($this->Home_model->blogname_check_model($blogname))
 		 return true;
    else
        {
        	$this->form_validation->set_message('blogname_check', 'The blogname '.$blogname.' has already been used by you. Please choose a different title.');
        	return false;

        } 

   }

   else
      {
      //redirect(base_url('/UserRegister'));
        redirect('HomeController/userregView');
      }
   
  }
  

public function searchAction()
{

 if($this->Home_model->_check_module_task_auth())
 {
 



	//$this->load->library('form_validation');
           
     // $this->form_validation->set_data();  
     
     //this->form_validation->set_rules('search_mail','Search Email','required|valid_email');

/*
      if($this->form_validation->run()==FALSE)
		{     

			

			$this->load->view('UserHomeView');


		}
		else
		{

      */


            $data['email'] = $this->session->userdata('email');
            
                   
             $data['search_mail'] = $this->input->get('search_mail');

           
            $result_fullname = $this->Home_model->searchBloggers_model($data);

            if(isset($result_fullname))
            {
              ///////show search results  
            	//echo 'success';
                   
                    $result_data['name'] = $result_fullname;
                    $result_data['search_mail'] = $data['search_mail'];
                    $name = $result_data['name'];
                    $mail = $result_data['search_mail'];

                    echo $result_data['name'];
                    echo nbs(3);
                    echo anchor('/HomeController/getBlogs/'.$mail.'/'.$name,'Read Blogs');
                    echo nbs(3);
                    echo anchor('/HomeController/subscribeAction/'.$mail.'/'.$name,'Subscribe Blogger');

                    $this->load->View('UserHomeView');
                     //redirect('/HomeController/searchAction');
            }
            else
            {
              echo "Sorry! We did not find any blogger with this mail.";
              $this->load->view('UserHomeView');
            }
           

//		}

  }



else
      {
        //redirect(base_url('/UserRegister'));
        redirect('HomeController/userregView');
      }
  

}



public function getBlogs()
{

 if($this->Home_model->_check_module_task_auth())
 {

 $Blogger_mail =  urldecode($this->uri->segment(3));
 $Blogger_name =   urldecode($this->uri->segment(4));


 //echo $Blogger_name;
 //echo $Blogger_mail;



 $result_array = array();

 $result_array = $this->Home_model->getBlogs_model($Blogger_mail,$Blogger_name);

 /*if($result_array)
     echo "success";
 else
     echo "failure";   
*/

 if(!empty($result_array))
 {

 // $data = array(); 
  $temp = array();
  //$row = array();

  $i =0;

  //echo $result_array[0]->blog_topic;

  foreach ($result_array as $row) {

     
     $temp[$i]['blog_topic'] = $row['blog_topic']; 
     $temp[$i]['content'] = $row['content']; 

    //echo $row['blog_topic'];
    //echo br();
    //echo $row['content'];
    //echo br(3);

     $i++;
 }
 
    $data['temp_array'] = $temp;
    $data['Blogger_name'] = $Blogger_name;
 
   $this->load->view('BlogView',$data);

    //*/
 }    
 
 else{
  
  echo 'No results found.(May be the user has not blogged yet.)';

 }
     
}

else
      {
       //redirect(base_url('/UserRegister'));
        redirect('HomeController/userregView');
      }

}


public function subscribeAction()
{

if($this->Home_model->_check_module_task_auth())
{
 
$Blogger_mail =  urldecode($this->uri->segment(3));
$Blogger_name =   urldecode($this->uri->segment(4));

  if($this->session->userdata("email")==$Blogger_mail)
  {
    echo '<h4 color="red">You could not subscribe to yourself.</h4>';
  }

else{


  if($this->Home_model->subscribeStatus_check($Blogger_mail))
  {  
    $res = $this->Home_model->subscribeAction_model($Blogger_mail,$Blogger_name);

   // echo print_r($res);

  if($res)
  {
      echo 'subscribed';
  }

  else{
    echo 'Could not subscribe. Try Again';
  }
  
}

else{

  echo 'already subscribed';
}

}


}

else
      {
        //redirect(base_url('/UserRegister'));
        redirect('HomeController/userregView');
      }

}


   public function logoutAction()
   {
    $this->session->unset_userdata('email');

$this->session->sess_destroy();
     // $this->load->driver('cache');
    
    //$this->cache->clean();
  
   //redirect(base_url('/UserRegister'));
    redirect('HomeController/userregView');

   }


  public function userhomeView()
  {
   $this->load->view('UserHomeView');
  }

  public function userregView()
  {
   $this->load->view('UserRegister');
  }

}



