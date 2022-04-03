import React from "react";
import style from "./arrows.module.css";
import { Icon } from "@iconify/react";
import Slider from "react-slick";

const Arrows = ({ direction, clickFunction }) => {
  return (
    <>
      {direction === "right" && (
        <span
          className={style["leftArrow"]}
          onClick={() => {
            clickFunction.current.slickNext();
          }}
        >
          <Icon icon="bi:arrow-right-circle" width="35" color="#000000" />
        </span>
      )}

      {direction === "left" && (
        <>
          <span
            className={style["rightArrow"]}
            onClick={() => {
              clickFunction.current.slickPrev();
            }}
          >
            <Icon icon="bi:arrow-left-circle" width="35" color="#000000" />
          </span>
        </>
      )}
    </>
  );
};

export default Arrows;
