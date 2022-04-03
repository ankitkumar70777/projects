import React from "react";
import { slide as Menu } from "react-burger-menu";
import { Icon } from "@iconify/react";
import "./sidebar.css";
import style from "./sidebar.module.css";

const Sidebar = (props) => {
  return (
    <>
      <Menu
        {...props}
        isOpen={props.sidebarObj.sideBar}
        onClose={() => {
          props.sidebarObj.setSideBar(false);
        }}
        right
      >
        <div className={style["wapMenu"]}>
          <a>
            <Icon icon="mdi:phone" width="25" color="#004dcf" />
            <Icon icon="logos:whatsapp" color="#004dcf" width="25" />
            <span className={`${style["menu1"]}`}>91 - 8806102316</span>

            <span className={`${style["contactTime"]} `}>10AM - 05PM</span>
          </a>
          <a>
            <Icon icon="mdi:email" width="25" color="#b80000" />
            <span className={`${style["email"]} ${style["menu1"]}`}>
              {" "}
              amitravidas555@gmail.com
            </span>
          </a>
          <a>
            <Icon icon="mdi:account" width="25" />
            <span className={`${style["menu2"]}`}>login/register</span>
          </a>
          <a>
            <Icon icon="mdi:truck-fast" width="25" color="#004d40" />
            <span className={`${style["menu2"]}`}>tract your order</span>
          </a>
          <a>
            <Icon icon="mdi:cart-minus" width="25" />
            <span className={`${style["menu2"]}`}>cart</span>
          </a>
        </div>
      </Menu>
    </>
  );
};

export default Sidebar;
