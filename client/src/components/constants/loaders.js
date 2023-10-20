import React from "react";
import ReactLoading from "react-loading";
import { useTranslation } from "react-i18next";

export const Loader = () => {
  return (
    <ReactLoading
      type={"spinningBubbles"}
      color={"#fff"}
      height={20}
      width={20}
    />
  );
};

export const ActionsLoader = () => {
  return <ReactLoading type={"spin"} color={"#000"} height={20} width={20} />;
};

export const DataLoader = () => {
  return (
    <div className={"mt-3"}>
      <ReactLoading type={"bars"} color={"#000"} height={80} width={50} />
    </div>
  );
};

export const TaskBoardLoader = () => {
  const { t } = useTranslation();
  return (
    <div className={"mt-5"}>
      <div className={"w-100 d-flex justify-content-center"}>
        <ReactLoading
          type={"spokes"}
          color={"#000"}
          height={"10%"}
          width={"10%"}
        />
      </div>
      <div className={"w-100 d-flex justify-content-center"}>
        <h6 style={{ marginTop: "30px" }}>{t("loaders.pleaseWait")}...</h6>
      </div>
    </div>
  );
};

export const CalendarLoader = () => {
  return (
    <ReactLoading type={"bubbles"} color={"#000"} height={30} width={50} />
  );
};

export const SuggestionLoader = () => {
  return (
    <ReactLoading type={"bubbles"} color={"#000"} height={50} width={50} />
  );
};
