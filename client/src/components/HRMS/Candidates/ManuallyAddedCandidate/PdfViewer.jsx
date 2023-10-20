import React, { useState, useCallback, useEffect } from "react";
import { getWindowDimensions } from "../../../Shared/WindowDimensions";
import { Col } from "reactstrap";
import { Document, Page, pdfjs } from "react-pdf";
import "react-pdf/dist/esm/Page/AnnotationLayer.css";
import "react-pdf/dist/esm/Page/TextLayer.css";
import { GrFormPrevious, GrFormNext } from "react-icons/gr";
import {
  highlightFieldColor,
  highlightClickedFieldColor,
  redColor,
} from "../../../Shared/Colors";
import file from "../../../Shared/marked-b64_1.pdf";

pdfjs.GlobalWorkerOptions.workerSrc = `//unpkg.com/pdfjs-dist@${pdfjs.version}/legacy/build/pdf.worker.min.js`;

const PdfViewer = ({
  cvFile,
  cvData,
  searchKeywords,
  clickedSearchKeywords,
}) => {
  const [numPages, setNumPages] = useState(null);
  const [pageNumber, setPageNumber] = useState(1);
  const [otherWords, setOtherWords] = useState([]);

  const [windowDimensions, setWindowDimensions] = useState(
    getWindowDimensions()
  );

  let specialCharacters = /[&\/\\#,+()$~%.'":*?<>{}]/g;

  // useEffect(() => {
  //   if (cvData) {
  //     let cvDataArr = [];
  //     for (let [key, value] of Object.entries(cvData)) {
  //       for (const candidateData in value) {
  //         if (Object.hasOwnProperty.call(value, candidateData)) {
  //           const element = value[candidateData];
  //           element.split(" ").map((item) => {
  //             item = item.replaceAll(specialCharacters, "");
  //             if (item.length > 2) {
  //               item = item.toLowerCase();
  //               cvDataArr.push(item);
  //             }
  //           });
  //         }
  //       }
  //     }

  //     // SEARCH KEYWORDS CONVERTS INTO LOWERCASE
  //     let searchKeywordsLowerCase = searchKeywords.map((item) => {
  //       return item.toLowerCase();
  //     });

  //     // FILTER CV DATA AND SEARCH KEYWORDS ARRAY
  //     cvDataArr = cvDataArr.filter(
  //       (val) => !searchKeywordsLowerCase.includes(val)
  //     );

  //     if (cvDataArr.length > 0) {
  //       let uniqueArr = [...new Set(cvDataArr)];
  //       let filteredcandidateCvData = uniqueArr.filter((item) => {
  //         return item != "" && item != "undefined";
  //       });
  //       setOtherWords(filteredcandidateCvData);
  //     }
  //   }
  // }, [cvData]);

  const onDocumentLoadSuccess = ({ numPages }) => {
    setNumPages(numPages);
  };

  const changePage = (offset) => {
    setPageNumber((prevPageNumber) => prevPageNumber + offset);
  };

  const previousPage = () => {
    changePage(-1);
  };

  const nextPage = () => {
    changePage(1);
  };

  // const highlightPattern = (text, value) => {
  //   let search = new RegExp(value, "gi");
  //   return text.replace(
  //     search,
  //     (value) =>
  //       `<mark style='background-color: ${highlightFieldColor}'>${value}</mark>`
  //   );
  // };

  // const highlightPatternForOther = (text, value) => {
  //   let search = new RegExp(value, "gi");
  //   return text.replace(
  //     search,
  //     (value) => `<mark style='background-color: ${redColor}'>${value}</mark>`
  //   );
  // };

  const highlightPatternForClickedFields = (text, value) => {
    let search = new RegExp(value, "gi");
    return text.replace(
      search,
      (value) =>
        `<mark style='background-color: ${highlightClickedFieldColor}'>${value}</mark>`
    );
  };

  const textRenderer = useCallback(
    (textItem) => {
      let text = textItem.str;

      // if (otherWords.length > 0) {
      //   otherWords.forEach((value) => {
      //     value = value.replace(new RegExp(" ", "gi"), "|");
      //     let search = new RegExp(value, "gi");
      //     if (text.match(search) != null) {
      //       text = highlightPatternForOther(text, value);
      //     }
      //   });
      // }

      // if (searchKeywords.length > 0) {
      //   searchKeywords.forEach((value, index) => {
      //     value = value.replace(new RegExp(" ", "gi"), "|");
      //     let search = new RegExp(value, "gi");
      //     if (text.match(search) != null) {
      //       text = highlightPattern(text, value);
      //     }
      //   });
      // }

      if (clickedSearchKeywords.length > 0) {
        clickedSearchKeywords.forEach((value) => {
          let search = new RegExp(value, "gi");
          if (text.match(search) != null) {
            text = highlightPatternForClickedFields(text, value);
          }
        });
      }

      return text;
    },
    [searchKeywords, otherWords, clickedSearchKeywords]
  );

  return (
    <Col
      sm={4}
      md={4}
      className={"sticky-cv"}
      style={{
        height: windowDimensions.height,
        marginBottom: "15%",
      }}
    >
      <Document file={cvFile} onLoadSuccess={onDocumentLoadSuccess}>
        <Page pageNumber={pageNumber} customTextRenderer={textRenderer} />
      </Document>
      <div className="w-100 d-flex justify-content-center">
        <button
          type="button"
          disabled={pageNumber <= 1}
          onClick={previousPage}
          className={"bg-none border-0 p-0 mr-2"}
        >
          <GrFormPrevious />
        </button>
        <p className="mb-0">
          Page {pageNumber || (numPages ? 1 : "--")} / {numPages || "--"}
        </p>
        <button
          type="button"
          disabled={pageNumber >= numPages}
          onClick={nextPage}
          className={"bg-none border-0 p-0 ml-2"}
        >
          <GrFormNext />
        </button>
      </div>
    </Col>
  );
};

export default PdfViewer;
