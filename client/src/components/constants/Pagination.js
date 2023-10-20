import React, { useState } from "react";
import Paginator from "react-hooks-paginator";

export const Pagination = ({ totalRecords, pageNumber, setPageNumber }) => {
  const [offset, setOffset] = useState(0);
  return (
    <Paginator
      totalRecords={totalRecords}
      pageLimit={10}
      pageNeighbours={2}
      setOffset={setOffset}
      currentPage={pageNumber}
      setCurrentPage={setPageNumber}
    />
  );
};
