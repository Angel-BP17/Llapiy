import { describe, it, expect } from 'vitest';
import { render, screen } from '@testing-library/react';
import React from 'react';

describe('Example Test', () => {
    it('should pass', () => {
        render(<h1>Hello World</h1>);
        expect(screen.getByText('Hello World')).toBeInTheDocument();
    });
});
