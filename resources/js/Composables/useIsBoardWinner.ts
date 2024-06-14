export function useIsBoardWinner(selectedNumbers: (number|string)[], boardNumbers: (number|string)[][]) {
    const rowsDetected = winnerRowsFound(selectedNumbers, boardNumbers);
    const columnsDetected = winnerColumnsFound(selectedNumbers, boardNumbers);
    const diagonalsDetected = winnerDiagonalsFound(selectedNumbers, boardNumbers);
    const cornersDetected = winnerCornerFound(selectedNumbers, boardNumbers);
    const completed = rowsDetected + columnsDetected + diagonalsDetected + cornersDetected;

    if (cornersDetected > 0) {
        return true;
    }

    return completed > 1;
}

function winnerRows(board: (number|string)[][]): (number|string)[][] {
    return board[0].map((_, colIndex) => board.map(row => row[colIndex]));
}

function winnerColumns(board: (number|string)[][]): (number|string)[][] {
    return board;
}

function winnerDiagonals(board: (number|string)[][]): (number|string)[][] {
    const min = 0;
    const max = 4;
    const forwardDiagonal = [];
    const backwardDiagonal = [];

    for (let i = min; i <= max; i++) {
        forwardDiagonal.push(board[i][i]);
        backwardDiagonal.push(board[max - i][i]);
    }

    return [forwardDiagonal, backwardDiagonal];
}

function winnerCorner(board: (number|string)[][]): (number|string)[] {
    return [board[0][0], board[0][4], board[4][0], board[4][4]];
}

function winnerRowsFound(selectedNumbers: (number|string)[], boardNumbers: (number|string)[][]): number {
    let rowsDetected = 0;

    winnerRows(boardNumbers).forEach(row => {
        if (row.every(record => selectedNumbers.includes(record))) {
            rowsDetected++;
        }
    })

    return rowsDetected;
}

function winnerColumnsFound(selectedNumbers: (number|string)[], boardNumbers: (number|string)[][]): number {
    let columnsDetected = 0;

    winnerColumns(boardNumbers).forEach(column => {
        if (column.every(record => selectedNumbers.includes(record))) {
            columnsDetected++;
        }
    })

    return columnsDetected;
}

function winnerDiagonalsFound(selectedNumbers: (number|string)[], boardNumbers: (number|string)[][]): number {
    let diagonalsDetected = 0;

    winnerDiagonals(boardNumbers).forEach(diagonal => {
        if (diagonal.every(record => selectedNumbers.includes(record))) {
            diagonalsDetected++;
        }
    })

    return diagonalsDetected;
}

function winnerCornerFound(selectedNumbers: (number|string)[], boardNumbers: (number|string)[][]): number {
    let cornersDetected = 0;

    if (winnerCorner(boardNumbers).every(record => selectedNumbers.includes(record))) {
        cornersDetected++;
    }

    return cornersDetected;
}
