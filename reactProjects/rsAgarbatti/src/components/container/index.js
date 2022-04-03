import React, { useState } from "react";
import Sidebar from "../navbar1/components/sidebar";
import style from "./index.module.css";
import Navbar1 from "../navbar1";
import Navbar2 from "../navbar2";
import ProductSlide from "../productSlide";

import { Icon } from "@iconify/react";

const MainContainer = () => {
  const [sideBar, setSideBar] = useState(false);
  console.log({ sideBar });
  return (
    <div className={`px-1  mx-auto h-full  ${style.componentWidth}`}>
      <Sidebar
        // pageWrapId={"page-wrap"}
        // outerContainerId={"navbar1"}
        sidebarObj={{ sideBar, setSideBar }}
      />
      <Navbar1 sidebarObj={{ sideBar, setSideBar }} />
      <Navbar2 />
      <ProductSlide />
    </div>
  );
};

export default MainContainer;
