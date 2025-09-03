<!-- <div class="overlay-div" onclick="_close_menu()"></div>
<div class="slide-side-div" >
    <div class="div-in">
        <ul>
            <li>HOME</li>
            <li>PROFILE</li>
            <li>ABOUT US</li>
        </ul>
        <a href="?php echo $website_url ?>/admin/"><li><button class="btn_list">Sign Up</button></li></a>
        <a href="?php echo $website_url ?>/admin"><li><button class="btn_list">Login</button></li></a> 
    </div>
</div>    -->

<div class="overlay-div" onclick="_close_menu()"></div>
    <div class="slide-side-div" >
        <div class="div-in">
        <h2>TEMIDIRE COOPERATIVE</h2>
        
        <label>
                <div class="lang-div profile"  onclick="show_account_setting();"  action = "update_profile_pix" title="show profile">
                <?php if ($passport==''){?>
                <img src="<?php echo $website_url; ?>/uploaded_files/profile_pix/1.jpg" id="my_passport3" alt="profile picture"/>
                    <?php } else {?>
                        <img src="<?php echo $website_url; ?>/uploaded_files/profile_pix/<?php echo $passport; ?>" id="my_passport" alt="profile picture"/>
                    <?php } ?>
                </div>
        </label>
        <ul>
                
                <li><p id="first" onclick="_next_page('next_1'), fetch_all_entries(), highlite('first');"><i class="bi-speedometer"></i> Dashboard</p></li>
                <li><p id="second" onclick="_next_page('next_2'), restore_div(), highlite('second');"><i class="bi-person-gear"></i> Setting</p></li>
                <!-- <input type="hidden" name="action" value="logout"/>     -->
                <div class="nav-div" ></div>  
                <li onclick="logout();"><p> <i class="bi-power"></i>  Log-Out</li></p>    
                </ul>
        </div>
    </div>







    
   
