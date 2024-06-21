CHECK TABLE `mysql`.`user`;
REPAIR TABLE `mysql`.`user`;
CHECK TABLE `mysql`.`db`;
REPAIR TABLE `mysql`.`db`;





(
    SELECT 
        `User`, 
        `Host`, 
        `Select_priv`, 
        `Insert_priv`, 
        `Update_priv`, 
        `Delete_priv`, 
        `Create_priv`, 
        `Drop_priv`, 
        `Grant_priv`, 
        `Index_priv`, 
        `Alter_priv`, 
        `References_priv`, 
        `Create_tmp_table_priv`, 
        `Lock_tables_priv`, 
        `Create_view_priv`, 
        `Show_view_priv`, 
        `Create_routine_priv`, 
        `Alter_routine_priv`, 
        `Execute_priv`, 
        `Event_priv`, 
        `Trigger_priv`, 
        '*' AS `Db`, 
        'g' AS `Type`
    FROM 
        `mysql`.`user`
)
UNION
(
    SELECT 
        `User`, 
        `Host`, 
        `Select_priv`, 
        `Insert_priv`, 
        `Update_priv`, 
        `Delete_priv`, 
        `Create_priv`, 
        `Drop_priv`, 
        `Grant_priv`, 
        `Index_priv`, 
        `Alter_priv`, 
        `References_priv`, 
        `Create_tmp_table_priv`, 
        `Lock_tables_priv`, 
        `Create_view_priv`, 
        `Show_view_priv`, 
        `Create_routine_priv`, 
        `Alter_routine_priv`, 
        `Execute_priv`, 
        `Event_priv`, 
        `Trigger_priv`, 
        `Db`, 
        'd' AS `Type`
    FROM 
        `mysql`.`db`
    WHERE 
        'wanawat_tracking' LIKE `Db`
)
ORDER BY 
    `User` ASC, 
    `Host` ASC, 
    `Db` ASC
LIMIT 0, 25;
