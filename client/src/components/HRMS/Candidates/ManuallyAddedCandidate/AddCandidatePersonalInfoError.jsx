import React from "react";
import { Card, CardBody, Row } from "reactstrap";
import { useSelector } from "react-redux";
import { useTranslation } from "react-i18next";

const AddCandidatePersonalInfoError = () => {
  const { t } = useTranslation();

  const settingsSelector = useSelector((state) => state.settings);

  return (
    <>
      <Card>
        <CardBody>
          <Row className={"w-100 d-flex justify-content-center"}>
            <h5
              style={{
                fontSize: `${
                  settingsSelector.FontSize === "Large"
                    ? "1.75rem"
                    : settingsSelector.FontSize === "Extra Large"
                    ? "2.25rem"
                    : "1.25rem"
                }`,
              }}
            >
              {t("warnings.addCandidateBookmarkWarning")}
            </h5>
          </Row>
        </CardBody>
      </Card>
    </>
  );
};

export default AddCandidatePersonalInfoError;
