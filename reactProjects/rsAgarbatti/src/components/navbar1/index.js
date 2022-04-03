import React, { useState } from "react";

import style from "./index.module.css";
import { Icon } from "@iconify/react";
import logo from "../media/images/logo.jpeg";

const Navbar1 = ({ sidebarObj }) => {
  return (
    <>
      <div className={``} id="navbar1">
        <div
          className={`flex flex-row justify-between mx-3  mt-1 ${style.navBar1}`}
        >
          <div>
            <img src={logo} className={`${style.logo}`} alt="main logo" />
          </div>
          <div
            className={`${style.mainMenus} flex flex-col justify-between items-center mx-3  mt-1 `}
          >
            <div className={`${style["searchProducts"]}`}>
              <span>
                <input
                  type="text"
                  name="products"
                  placeholder="search products like 'mogra' 'dhoop'"
                  id="products"
                />
                <button>
                  <Icon icon="bx:search-alt-2" width="25" />
                </button>
              </span>
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
            <div className={`flex flex-row justify-end w-full`}>
              <a>about us</a>
              <a>blog</a>
              <a>gifting</a>
            </div>
          </div>
          <div className={`${style["mobileMenuButton"]}`}>
            <button
              onClick={() => {
                sidebarObj.setSideBar(!sidebarObj.sideBar);
              }}
            >
              <Icon icon="charm:menu-hamburger" width="25" />
            </button>
          </div>
        </div>
      </div>
    </>
  );
};

export default Navbar1;
