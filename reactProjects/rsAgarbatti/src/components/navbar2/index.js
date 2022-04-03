import React from "react";
import style from "./index.module.css";

const Navbar2 = () => {
  return (
    <div className={`${style["productListNav"]}`}>
      <div className={`${style["middle"]}`}>
        <a href="#">Home</a>
        <a href="#">Agarbathies</a>
        <a href="#">Dhoop, Cone & Sambrani</a>
        <a href="#">pooja samagri</a>
      </div>
    </div>
  );
};

export default Navbar2;
