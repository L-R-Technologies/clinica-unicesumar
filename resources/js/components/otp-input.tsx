import {
    useRef,
    type ClipboardEvent,
    type ChangeEvent,
    type FocusEvent,
    type KeyboardEvent,
} from 'react';
import { Input } from '@/components/ui/input';
import { cn } from '@/lib/utils';

const DEFAULT_OTP_LENGTH = 6;

interface OtpInputProps {
    readonly id?: string;
    readonly length?: number;
    readonly value: string;
    readonly onChange: (value: string) => void;
    readonly autoFocus?: boolean;
    readonly className?: string;
}

export function OtpInput({
    id,
    length = DEFAULT_OTP_LENGTH,
    value,
    onChange,
    autoFocus = false,
    className,
}: OtpInputProps) {
    const inputRefs = useRef<Array<HTMLInputElement | null>>([]);

    function focusInput(index: number): void {
        inputRefs.current[index]?.focus();
    }

    function setDigitAt(index: number, digit: string): string {
        const chars = Array.from({ length }, (_, i) => value[i] ?? '');
        chars[index] = digit;
        return chars.join('').slice(0, length);
    }

    function submitWhenComplete(index: number, nextValue: string): void {
        if (nextValue.length !== length) {
            return;
        }

        const form = inputRefs.current[index]?.form;

        // Adia o submit para o próximo tick, garantindo que o estado do
        // formulário já reflita o último dígito digitado.
        if (form) {
            window.setTimeout(() => form.requestSubmit(), 0);
        }
    }

    function fillFrom(index: number, digits: string): void {
        const chars = Array.from({ length }, (_, i) => value[i] ?? '');
        digits.split('').forEach((digit, offset) => {
            if (index + offset < length) {
                chars[index + offset] = digit;
            }
        });
        const nextValue = chars.join('').slice(0, length);
        onChange(nextValue);
        focusInput(Math.min(index + digits.length, length - 1));
        submitWhenComplete(index, nextValue);
    }

    function handleChange(
        index: number,
        event: ChangeEvent<HTMLInputElement>,
    ): void {
        const digits = event.target.value.replace(/\D/g, '');

        if (digits === '') {
            onChange(setDigitAt(index, ''));
            return;
        }

        if (digits.length === 1) {
            const nextValue = setDigitAt(index, digits);
            onChange(nextValue);
            if (index < length - 1) {
                focusInput(index + 1);
            }
            submitWhenComplete(index, nextValue);
            return;
        }

        fillFrom(index, digits);
    }

    function handlePaste(
        index: number,
        event: ClipboardEvent<HTMLInputElement>,
    ): void {
        event.preventDefault();
        const digits = event.clipboardData
            .getData('text')
            .replace(/\D/g, '')
            .slice(0, length);

        if (digits !== '') {
            fillFrom(index, digits);
        }
    }

    function handleKeyDown(
        index: number,
        event: KeyboardEvent<HTMLInputElement>,
    ): void {
        if (event.key === 'Backspace' && !value[index] && index > 0) {
            event.preventDefault();
            onChange(setDigitAt(index - 1, ''));
            focusInput(index - 1);
            return;
        }

        if (event.key === 'ArrowLeft' && index > 0) {
            event.preventDefault();
            focusInput(index - 1);
            return;
        }

        if (event.key === 'ArrowRight' && index < length - 1) {
            event.preventDefault();
            focusInput(index + 1);
        }
    }

    return (
        <div className={cn('flex justify-center gap-2', className)}>
            {Array.from({ length }, (_, index) => (
                <Input
                    key={index}
                    ref={(element) => {
                        inputRefs.current[index] = element;
                    }}
                    id={index === 0 ? id : undefined}
                    type="text"
                    inputMode="numeric"
                    autoComplete={index === 0 ? 'one-time-code' : 'off'}
                    aria-label={`Dígito ${index + 1} do código`}
                    className="size-11 p-0 text-center font-mono text-lg"
                    value={value[index] ?? ''}
                    onChange={(event) => handleChange(index, event)}
                    onPaste={(event) => handlePaste(index, event)}
                    onKeyDown={(event) => handleKeyDown(index, event)}
                    onFocus={(event: FocusEvent<HTMLInputElement>) =>
                        event.target.select()
                    }
                    autoFocus={autoFocus && index === 0}
                />
            ))}
        </div>
    );
}
