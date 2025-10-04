 <?php
    $ticket_analysis = new TKM_Analysis();
    $employees = $ticket_analysis->get_employee_ticket_stats();

    ?>
 <div class="dashboard-container">
     <h1>داشبورد تحلیل سیستم تیکت پشتیبانی</h1>
     <br>
     <div class="stats">
         <div class="stat-card">
             <h2> کل تیکت‌ها</h2>
             <p id="total-tickets"><?php echo $ticket_analysis->count_tickets() ?></p>
         </div>
         <div class="stat-card">
             <h2> کل پاسخ‌ها</h2>
             <p id="total-replies"><?php echo $ticket_analysis->count_replys() ?></p>
         </div>
         <div class="stat-card">
             <h2>میانگین امتیاز</h2>
             <p id="avg-rating"><?php echo $ticket_analysis->get_average_ticket_rating() ?> </p>
         </div>
         <div class="stat-card">
             <h2>کارمند پاسخگو</h2>
             <p id="active-support-tickets"><?php echo $ticket_analysis->count_users() ?></p>
         </div>
         <div class="stat-card">
             <h2> رضایت کاربران</h2>
             <p id="active-support-tickets"><?php echo $ticket_analysis->get_user_satisfaction_description() ?></p>
         </div>
     </div>
 </div>
 <div class="table-container">
     <h1 style="text-align: center;">کاربران پاسخگو</h1>
     <br>
     <table class="ticket-table">
         <thead>
             <tr>
                 <th>نام کارمند</th>
                 <th>تعداد تیکت‌ها</th>
                 <th>تیکت های باز </th>
                 <th>تیکت های پاسخ داده </th>
                 <th>میانگین امتیاز </th>
             </tr>
         </thead>
         <tbody>
             <?php if ($employees): ?>
                 <?php foreach ($employees as $employee): ?>
                     <tr>
                         <td><?php echo get_userdata($employee->user_id)->display_name; ?></td>
                         <td><?php echo $employee->ticket_count; ?></td>
                         <td>
                         <?php echo $employee->open_ticket_count; ?>
                         </td>
                         <td>
                         <?php echo $employee->answered_ticket_count; ?>
                         </td>
                         <td><?php echo round($employee->avg_rating, 2); ?></td>
                     </tr>
                 <?php endforeach; ?>
             <?php else: ?>
                 <tr>
                     <td colspan="4">داده‌ای یافت نشد</td>
                 </tr>
             <?php endif; ?>
         </tbody>
     </table>
 </div>