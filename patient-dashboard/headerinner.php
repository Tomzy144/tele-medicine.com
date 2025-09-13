
<div class="dash-header" id="change3">
  <div class="header-inner-div">
    <div class="search-div" id="change4">
      <div class="search-box" id="search_box">
        <input type="text" onkeyup="entry_search();" id="search_input" placeholder="Search by Month...">  <i class="bi-search"></i>
      </div>
    </div>

    <!-- <div class="switch-div" onclick="changer();" id="change5">
      <div class="circle"  id="x"> </div>
    </div> -->
    <div class="right-nav-div"><i class="bi bi-list" onclick="_open_menu()"></i></div> 
    <div class="personal-div">
      <div class="inner-div" id="icons">
      

        <div class="lang-div mail-div">
          <!-- <i class="bi-envelope"></i> -->
        </div>


        <div class="lang-div notification-div" onclick="pop_notification();">
            <i class="bi-bell"></i>
            <span id="notification-count" class="notification-badge">0</span>
        </div>


      


<label >
        <div class="lang-div profile"  onclick="show_account_setting();"  action = "update_profile_pix" title="show profile">
        <?php if ($passport==''){?>
          <img src="<?php echo $website_url; ?>/uploaded_files/profile_pix/1.jpg" id="my_passport2" alt="profile picture"/>
              <?php } else {?>
                <img src="<?php echo $website_url; ?>/uploaded_files/profile_pix/<?php echo $passport; ?>" id="my_passport" alt="profile picture"/>
              <?php } ?>
        </div>
</label>
      </div>

    </div>
  </div>
  </div>
  
            